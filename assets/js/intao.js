let db = {};
let dbVersion = parseInt(_intao_db_version) || 1;
let dbName = 'intaodb_' + _taoh_plugin_name;
let index_name = "settings_" + crc32(_taoh_plugin_name);
const tabId = `tab_${Date.now()}`;
const intaoChannel = new BroadcastChannel('intao_channel');

// ObjectStores
let objStores = {
    data_store: {name: 'taoh_intaodb_data', options: {keyPath: 'taoh_data'}},
    ttl_store: {name: 'taoh_intaodb_TTL', options: {keyPath: 'taoh_ttl'}},
    api_store: {name: 'taoh_intaodb_API', options: {keyPath: 'taoh_api'}},
    common_store: {name: 'taoh_intaodb_common', options: {keyPath: 'taoh_common'}},
    ntw_store: {name: 'taoh_intaodb_NTW', options: {keyPath: 'taoh_ntw'}},
    ntw_meta_store: {name: 'taoh_ntw_meta', options: {keyPath: 'id'}},
    ask_store: {name: 'taoh_intaodb_asks', options: {keyPath: 'taoh_data'}},
    job_store: {name: 'taoh_intaodb_jobs', options: {keyPath: 'taoh_data'}},
    metrics_store: {name: 'taoh_intaodb_metrics', options: {keyPath: 'taoh_data'}},
    event_store: {name: 'taoh_intaodb_events', options: {keyPath: 'taoh_data'}},
    read_store: {name: 'taoh_intaodb_reads', options: {keyPath: 'taoh_data'}},
    dojo_store: {name: 'taoh_intaodb_dojo_goal', options: {keyPath : 'taoh_dojo_goal'}}
};

localStorage.setItem('indexedDBFailed', 'false');

// Setting variables for Temporary Fix
const dataStore = objStores.data_store.name;
const TTLStore = objStores.ttl_store.name;
const APIStore = objStores.api_store.name;
const ASKStore = objStores.ask_store.name;
const JOBStore = objStores.job_store.name;
const METIRCSStore = objStores.metrics_store.name;
const EVENTStore = objStores.event_store.name;
const READStore = objStores.read_store.name;
const DOJOStore = objStores.dojo_store.name;

/**
 * This function creates the IndexedDB and object stores.
 */
function createIntaoDb(db_name = dbName, db_version = dbVersion) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(db_name, db_version);

        request.onupgradeneeded = (event) => {
            const taodb = event.target.result;
            for (const [key, value] of Object.entries(objStores)) {
                if (taodb.objectStoreNames.contains(value.name)) {
                    taodb.deleteObjectStore(value.name);
                }
                taodb.createObjectStore(value.name, value.options);
            }
        };

        request.onsuccess = (event) => {
            const instance = event.target.result;

            instance.onclose = () => {
                console.warn('IndexedDB closed — clearing db instance');
                instance._isClosed = true;
                db[db_name] = null;
            };

            instance.onversionchange = () => {
                console.warn('Version change detected — closing DB');
                instance._isClosed = true;
                instance.close();
                db[db_name] = null;
            };

            instance.onerror = (e) => {
                console.error('DB error', e);
                instance._isClosed = true;
                db[db_name] = null;
            };

            // Mark as open and cache
            instance._isClosed = false;
            db[db_name] = instance;

            // Version consistency check
            if (instance.version !== db_version) {
                console.log('IndexedDB version mismatch — recreating IndexedDB');
                notifyRecreateIntaoDb(db_name, db_version);
                recreateIntaoDb(db_name, db_version).then(resolve).catch(reject);
            } else {
                localStorage.setItem('indexedDBFailed', 'false');
                resolve(instance);
            }
        };

        request.onerror = (event) => {
            localStorage.setItem('indexedDBFailed', 'true');
            const error = event.target.error;

            if (error.name === 'VersionError') {
                console.log(error.message, 'Recreating IndexedDB');
                notifyRecreateIntaoDb(db_name, db_version);
                recreateIntaoDb(db_name, db_version).then(resolve).catch(reject);
            } else {
                reject(error);
            }
        };

        request.onblocked = () => {
            console.warn('Database open blocked by another connection.');
        };
    });
}

/**
 * Get a reference to the IndexedDB instance.
 */
function getIntaoDb(db_name = dbName, db_version = dbVersion) {
    return new Promise((resolve, reject) => {
        const existingDb = db[db_name];

        // Return the cached db if it's open
        if (existingDb && !existingDb._isClosed) {
            resolve(existingDb);
        } else {
            createIntaoDb(db_name, db_version).then(resolve).catch(reject);
        }
    });
}

/**
 * This function handles transactions and object stores.
 */
async function withStore(storeName, mode, callback, retryCount = 1) {
    try {
        const taodb = await getIntaoDb();
        return new Promise((resolve, reject) => {
            try {
                const transaction = taodb.transaction(storeName, mode);
                const store = transaction.objectStore(storeName);

                const result = callback(store);

                if (result instanceof IDBRequest) {
                    result.onsuccess = () => resolve(result.result);
                    result.onerror = () => reject(result.error);
                } else if (result instanceof Promise) {
                    result.then(resolve).catch(reject);
                } else {
                    resolve(result);
                }

                transaction.onerror = (event) => reject(event.target.error);
            } catch (err) {
                reject(err);
            }
        });
    } catch (err) {
        if (retryCount > 0 && err instanceof DOMException && err.name === 'InvalidStateError') {
            console.warn(`Retrying withStore(${storeName}) after reconnect...`);
            return withStore(storeName, mode, callback, retryCount - 1);
        } else {
            throw err;
        }
    }
}

/**
 * Notify other tabs/processes that IndexedDB is being recreated.
 */
function notifyRecreateIntaoDb(db_name, db_version, tab_id = tabId) {
    intaoChannel.postMessage({type: 'recreateIntaoDb', dbName: db_name, dbVersion: db_version, tabId: tab_id});
}

/**
 * Delete and recreate the IndexedDB.
 */
function recreateIntaoDb(db_name, db_version) {
    return new Promise((resolve, reject) => {
        if (db[db_name]) {
            db[db_name].close();
            db[db_name] = null;
        }

        const deleteRequest = indexedDB.deleteDatabase(db_name);

        deleteRequest.onsuccess = () => {
            createIntaoDb(db_name, db_version).then(resolve).catch(reject);
        };

        deleteRequest.onerror = (err) => {
            reject(err);
        };
    });
}

// Listen for messages from other tabs
intaoChannel.onmessage = (event) => {
    console.log('Received message from other tab', event.data);
    if (event.data.type === 'recreateIntaoDb') {
        if (event.data.tabId !== tabId) {
            // window.open('', '_self').close();
            // window.location.href = 'about:blank';
            /*window.close();
            if (!window.closed) {
                alert('This tab should be closed because another tab is handling the request. Please close it manually.');
            }*/
        }
    }
};

// Main functionality
class IntaoDB {
    static setItem(storeName, data) {
        return withStore(storeName, 'readwrite', (store) => {
            if (storeName == dataStore || storeName == ASKStore || storeName == JOBStore || storeName == EVENTStore || storeName == METIRCSStore || storeName == READStore ) {
                if (data.taoh_data == index_name || data.taoh_ttl == index_name || data.taoh_api == index_name) {
                    return store.put(data);
                } else if (data.values.type == 'metrics') {
                    return store.put(data);

                }
                else {
                    //console.log('--------data.values.output--------',data.values.output)
                    if ((data.values.success == true || data.values.success == "true") &&
                     (data.values.output != null && data.values.output != undefined &&
                        data.values.output != "" && data.values.output != false)) {

                        return store.put(data);
                    }
                }
            } else {
                return store.put(data);
            }
        });
    }

    static getItem(storeName, key) {
        return withStore(storeName, 'readonly', (store) => store.get(key));
    }

    // Batch Get
    static getItems(storeName, keys) {
        return withStore(storeName, 'readonly', (store) => {
            const promises = keys.map(key => {
                return new Promise((resolve) => {
                    const request = store.get(key);
                    request.onsuccess = () => resolve(request.result);
                    request.onerror = () => resolve(null);
                });
            });
            return Promise.all(promises);
        });
    }

    // Batch Set
    static setItems(storeName, dataArray) {
        return withStore(storeName, 'readwrite', (store) => {
            const promises = dataArray.map(data => {
                return new Promise((resolve, reject) => {
                    const request = store.put(data);
                    request.onsuccess = () => resolve(true);
                    request.onerror = () => reject();
                });
            });
            return Promise.all(promises);
        });
    }

    static checkKeyExists(storeName, key) {
        return withStore(storeName, 'readonly', (store) => store.count(key))
            .then(result => result > 0);
    }

    static removeItem(storeName, key) {
        return withStore(storeName, 'readwrite', (store) => store.delete(key));
    }

    static getStore(storeName, type = 'readonly') {
        return withStore(storeName, type);
    }

    static clearStore(storeName) {
        return withStore(storeName, 'readwrite', (store) => store.clear());
    }
}

createIntaoDb().catch((err) => console.error('Open connection to IndexedDB failed', err));