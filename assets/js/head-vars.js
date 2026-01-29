        var userInfoList = {};
        var roomInfoList = {};
        var room_activeTabId = 'connections-tab';
        var lastPCMsgCheckedTimestamp = 0;
        var lastNTWMsgCheckedTimestamp = 0;
        var ft_ntw_reFetchRequired = false;
        var ft_ntw_isProcessing = false;
        var imageExistMap = new Map();
        var ntw_unreadCountModified = {};
        var checkOfflineMessage = 1;
        var syncOfflineMessagesDelay = 3000; // 3 seconds

        var frm_message_id = 0;
        var frm_reply_view = false;
        var lastFRMMsgCheckedTimestamp = 0;
        var ft_frm_reFetchRequired = false;
        var ft_frm_isProcessing = false;
        var frm_replyCountMap = new Map();

        var lastFRMReplyMsgCheckedTimestamp = 0;
        var ft_frm_reply_isProcessing = false;

        var chat_activeTabId = 'channel';
