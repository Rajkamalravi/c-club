async function constructUpgradeModalContent(event_output, my_pToken, rsvp_slug, isLoggedIn, social_token, trackingtoken) {
    social_token = social_token || '';
    trackingtoken = trackingtoken || '';

    const userInfo = await ft_getUserInfo(my_pToken, 'public');
    const my_profile_type = userInfo.type;

    const eventtoken = event_output.eventtoken;
    const conttoken_data = event_output.conttoken;
    const eventTicketTypes = conttoken_data.ticket_types || {};
    const eventSponsorWidgetType = conttoken_data.event_sponsor_levels || {};
    const is_social_share_enabled = conttoken_data.event_social_sharing;
    const organizerEmail = conttoken_data.org_email;

    const { response: metaResp } = await _getEventMetaInfo({ eventtoken });
    const metaOutput = metaResp?.output || {};
    const sponsorGroup = metaOutput?.event_sponsor || {};

    var sponsor_model_data = '';
    var sponsor_level_count = 0;

    const rsvpTicket = rsvp_slug ? (eventTicketTypes.find(function(t) { return t.slug === rsvp_slug; }) || {}) : {};

    $.each(eventSponsorWidgetType, function(k, widget) {
        if (!isValidProfileForSponsor(widget, eventTicketTypes, eventSponsorWidgetType, rsvpTicket, my_profile_type, isLoggedIn)) return true;

        var slug = widget.slug;
        var pricing = calculateTicketPricing(widget, 2, eventTicketTypes, is_social_share_enabled);
        var charging_price = pricing.charging_price;
        var price = pricing.price;
        var discount_percentage = pricing.discount_percentage;

        var full_content = generateFullContent(widget);

        var social_param = social_token ? '/socialshare/' + social_token : '';
        var shareUrl = trackingtoken ? updateShareUrl(trackingtoken, slug) : '';

        var upgradeLink = _taoh_site_url_root + '/events/event_sponsor/' + eventtoken + '/' + widget.slug + social_param;
        if (rsvp_slug) {
            var ticketType = eventTicketTypes.find(function(ticket) { return ticket.title === widget.award_ticket_type; }) || {};
            upgradeLink = _taoh_site_url_root + '/events/upgrade_rsvp/' + eventtoken + '/' + encodeURIComponent(ticketType?.title) + '/' + window._um_encodeCurrentUrl;
        }

        sponsor_model_data += generateSponsorHTML(widget, charging_price, price, full_content, isLoggedIn, rsvp_slug, upgradeLink, organizerEmail, is_social_share_enabled, discount_percentage, shareUrl);

        sponsor_level_count++;
    });

    if (sponsor_level_count > 0) {
        $('#upgradeModal #upgradeCards').html(sponsor_model_data);
        $('.upgrade_modal_btn_wrapper').show();
    } else {
        $('#upgradeModal #upgradeCards').html('<p class="px-3">No upgrade options available.</p>');
        $('.upgrade_modal_btn_wrapper').hide();
    }
}

function constructSponsorInfoPopup(eventtoken, eventSponsorWidgetType, user_profile_type, org_email, social_token, eventTicketTypes, event_form_version, is_social_share_enabled, trackingtoken, isLoggedIn) {
    user_profile_type = user_profile_type || '';
    org_email = org_email || '';
    social_token = social_token || '';
    is_social_share_enabled = is_social_share_enabled || 0;
    trackingtoken = trackingtoken || '';
    isLoggedIn = isLoggedIn || '';

    var evenKey = 'event_MetaInfo_' + eventtoken;
    var rsvp_slug = '';

    IntaoDB.getItem(objStores.event_store.name, evenKey).then(function(data) {
        if (data?.values) {
            var sponsor_data = data.values.output;
            var sponsorGroup = sponsor_data.event_sponsor || {};
            constructBoxForSponsor(rsvp_slug, eventSponsorWidgetType, sponsorGroup, user_profile_type, org_email, social_token, eventTicketTypes, event_form_version, is_social_share_enabled, trackingtoken, isLoggedIn);
        } else {
            constructBoxForSponsor(rsvp_slug, eventSponsorWidgetType, {}, user_profile_type, org_email, social_token, eventTicketTypes, event_form_version, is_social_share_enabled, trackingtoken, isLoggedIn);
        }
    });
}

function constructBoxForSponsor(rsvp_slug, eventSponsorWidgetType, sponsorGroup, user_profile_type, organizerEmail, social_token, eventTicketTypes, event_form_version, is_social_share_enabled, trackingtoken, isLoggedIn) {
    user_profile_type = user_profile_type || '';
    organizerEmail = organizerEmail || '';
    social_token = social_token || '';
    is_social_share_enabled = is_social_share_enabled || 0;
    trackingtoken = trackingtoken || '';
    isLoggedIn = isLoggedIn || '';

    var sponsor_model_data = '';

    if (Array.isArray(eventSponsorWidgetType)) {
        sponsor_model_data += '<div class="modal-dialog bg-white" role="document">' +
            '<div class="modal-content">' +
                '<div class="modal-header bg-white" style="border: none;">' +
                    '<h5 class="modal-title text-center mt-4 heading" id="myModalLabel" style="flex: 1;"></h5>' +
                    '<button type="button" class="btn" data-dismiss="modal" aria-label="Close">' +
                        '<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                            '<path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z" fill="#D3D3D3"/>' +
                        '</svg>' +
                    '</button>' +
                '</div>' +
                '<div class="modal-body d-flex mx-auto" style="gap: 24px;">';

        var sponsor_level_count = 0;

        var rsvpTicket = rsvp_slug ? (eventTicketTypes.find(function(t) { return t.slug === rsvp_slug; }) || {}) : {};

        $.each(eventSponsorWidgetType, function(k, widget) {
            if (!isValidProfileForSponsor(widget, eventTicketTypes, {}, rsvpTicket, user_profile_type, isLoggedIn)) return true;

            var slug = widget.slug;
            var pricing = calculateTicketPricing(widget, event_form_version, eventTicketTypes, is_social_share_enabled);
            var charging_price = pricing.charging_price;
            var price = pricing.price;
            var discount_percentage = pricing.discount_percentage;

            var full_content = generateFullContent(widget);

            var social_param = social_token ? '/socialshare/' + social_token : '';
            var shareUrl = trackingtoken ? updateShareUrl(trackingtoken, slug) : '';

            var upgradeLink = _taoh_site_url_root + '/events/event_sponsor/' + eventtoken + '/' + widget.slug + social_param;

            sponsor_model_data += generateSponsorHTML(widget, charging_price, price, full_content, isLoggedIn, '', upgradeLink, organizerEmail, is_social_share_enabled, discount_percentage, shareUrl);
            sponsor_level_count++;
        });

        sponsor_model_data += '</div></div></div></div>';

        $('#sponsorInfo').html(sponsor_model_data);
        handleEmptySponsors(sponsor_level_count);
    }
}

function isValidProfileForSponsor(widget, eventTicketTypes, eventSponsorWidgetType, rsvpTicket, user_profile_type, isLoggedIn) {
    var awardType = widget?.award_ticket_type;
    if (!awardType) return false;

    if (rsvpTicket?.slug) {
        if (taoh_title_desc_decode(rsvpTicket.title) === awardType) return false;

        var rsvp_slug = rsvpTicket.slug;

        return eventTicketTypes.some(function(t) {
            var applicable = t.applicable_to || [];
            if (!(applicable.includes('all') || applicable.includes(rsvp_slug))) return false;
            return taoh_title_desc_decode(t.title) === awardType;
        });
    } else {
        if (!isLoggedIn) {
            if (Array.isArray(widget.profiletype) && !widget.profiletype.includes('visitor') && !widget.profiletype.includes('all')) {
                return false;
            }
        } else {
            if (Array.isArray(widget.profiletype) && !widget.profiletype.includes(user_profile_type) && !widget.profiletype.includes('all')) {
                return false;
            }
        }
    }

    return true;
}

function calculateTicketPricing(widget, event_form_version, eventTicketTypes, is_social_share_enabled) {
    var charging_price = 0, price = 0, discount_percentage = 0;

    if (event_form_version == 2) {
        var TicketArr = eventTicketTypes.find(function(ticket) { return ticket.title === widget.award_ticket_type; }) || {};
        if (TicketArr.paid_ticket == 1) {
            charging_price = TicketArr.cost || 0;
            price = TicketArr.strike_cost;
        }
        if (is_social_share_enabled == 1) {
            discount_percentage = TicketArr.social_sharing_discount;
        }
    } else {
        charging_price = widget.charging_price;
        price = widget.price;
    }

    return { charging_price: charging_price, price: price, discount_percentage: discount_percentage };
}

function calculateTotalUsed(sponsorGroup, title) {
    var total_used = 0;
    sponsorGroup.forEach(function(value) {
        if ((value.sponsor_type)?.toLowerCase() == title?.toLowerCase()) total_used++;
    });

    return total_used;
}

function generateFullContent(widget) {
    var full_content = '';
    if (Array.isArray(widget.incentives)) {
        widget.incentives.forEach(function(m) {
            if (m) {
                full_content += '<li class="py-2"><p><span>' + m + '</span></p></li>';
            }
        });
    }
    return full_content;
}

function generateSponsorHTML(widget, charging_price, price, full_content, isLoggedIn, rsvp_slug, upgradeLink, organizerEmail, is_social_share_enabled, discount_percentage, shareUrl) {
    var isUpgrade = !!rsvp_slug;

    var html = '<div class="gold-card py-3">' +
        '<div>' +
            '<h6 class="label py-2 px-2">' + widget.title + ' ' + (widget.recommended == 1 ? '<p class="recommended-tag">RECOMMENDED</p>' : '<p class="recommended-tag">&nbsp;</p>') + '</h6>' +
            '<p class="event-price-tag py-2 mt-4 px-2">$' + charging_price + ' ' + (charging_price != price ? '(<span style="text-decoration: line-through;">$' + price + '</span>)' : '') + ' Per Event</p>' +
            '<ul class="px-3">' + (full_content || '<li class="py-2"><p><span>' + widget.title + ' Branding on Event Pages</span></p></li>') + '</ul>' +
        '</div>' +
        '<div>' +
            '<div class="d-flex px-3 ' + (!isLoggedIn ? 'flex-wrap' : '') + '" style="gap: 4px;">' +
                '<div class="' + (!isLoggedIn ? 'flex-grow-1' : '') + '" style="display:' + ((widget?.get_started !== undefined && widget.get_started != 1) ? 'none' : 'block') + '">';

    if (isLoggedIn) {
        html += '<a class="btn ' + (!isUpgrade ? 'get-started' : '') + ' upgrade-btn w-100" href="' + upgradeLink + '">' + (charging_price > 0 ? 'Buy Now' : 'Claim Now!') + '</a>';
    } else {
        html += '<button type="button" class="login-and-buy mt-3 mb-2 btn w-100 create_referral" data-location="' + upgradeLink + '" data-title="' + btoa(unescape(encodeURIComponent(widget.title))) + '" data-toggle="modal" data-target="#config-modal" data-dismiss="modal"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>' + (charging_price > 0 ? 'Login & Buy Now' : 'Login & Claim Now!') + '</button>';
    }

    html += '</div>';

    if (organizerEmail) {
        html += '<div class="flex-grow-1"><a class="btn ' + (!isUpgrade ? 'get-started' : '') + ' contact-us upgrade-contact-us-btn w-100" href="#" data-toggle="modal" data-target="#contactusModal" data-email="' + organizerEmail + '" data-dismiss="modal">Contact us</a></div>';
    }

    html += '</div>';

    if (isUpgrade) {
        html += '<ul class="d-flex px-3 mt-2 svg-styled">' +
            '<li>' +
                '<svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                    '<path d="M12.2923 0.0256658C9.82275 0.788638 6.88018 4.67069 4.05964 8.65749C2.86259 6.64193 1.59444 5.96894 0.283202 6.65849C0.121354 6.74348 0.0147307 6.90646 0.00139086 7.08976C-0.0119378 7.27495 0.0712083 7.4538 0.221637 7.56353C1.17242 8.25737 2.15876 9.4872 3.33173 11.4342V11.4336C3.54118 11.7869 3.922 12.0025 4.33201 12H4.35105C4.77376 11.9962 5.16093 11.7653 5.36593 11.3962C6.50712 9.32866 7.76061 7.32576 9.12076 5.39576C10.2353 3.81273 11.4781 2.3229 12.8363 0.942167C13.0191 0.763309 13.0527 0.481724 12.9175 0.264808C12.7912 0.0485417 12.531 -0.0510666 12.2923 0.0256658Z" fill="#3DB057"/>' +
                '</svg>' +
                '<span class="ml-1">Your previous payment will be refunded upon upgrading to this option.</span>' +
            '</li>' +
        '</ul>';
    }

    html += '<div class="pt-3" style="' + (is_social_share_enabled && discount_percentage > 0 ? '' : 'display:none') + '">' +
            '<ul class="d-flex px-3 svg-styled" style="gap: 4px;">' +
                '<li><svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.2923 0.0256658C9.82275 0.788638 6.88018 4.67069 4.05964 8.65749C2.86259 6.64193 1.59444 5.96894 0.283202 6.65849C0.121354 6.74348 0.0147307 6.90646 0.00139086 7.08976C-0.0119378 7.27495 0.0712083 7.4538 0.221637 7.56353C1.17242 8.25737 2.15876 9.4872 3.33173 11.4342V11.4336C3.54118 11.7869 3.922 12.0025 4.33201 12H4.35105C4.77376 11.9962 5.16093 11.7653 5.36593 11.3962C6.50712 9.32866 7.76061 7.32576 9.12076 5.39576C10.2353 3.81273 11.4781 2.3229 12.8363 0.942167C13.0191 0.763309 13.0527 0.481724 12.9175 0.264808C12.7912 0.0485417 12.531 -0.0510666 12.2923 0.0256658Z" fill="#3DB057"/></svg>' +
                '<span class="ml-1">Share and get ' + discount_percentage + '% discount for this ticket</span></li>' +
            '</ul>' +
            '<div class="social_sharing d-flex px-3 pt-3" style="gap: 4px;">' +
                '<button class="btn get-started upgrade-btn sponsor-share-click" data-toggle="modal" data-url="' + shareUrl + '" data-target="#shareModal" data-dismiss="modal" aria-label="Close">Share for ' + discount_percentage + '% Off</button>' +
            '</div>' +
        '</div>' +
        '</div>' +
    '</div>';

    return html;
}

function updateShareUrl(trackingtoken, slug) {
    var shareUrl = $("#share_link").val();
    return shareUrl.replace(trackingtoken + '/stlo', trackingtoken + '/' + slug + '/stlo');
}

function handleEmptySponsors(sponsor_level_count) {
    setTimeout(function() {
        if (sponsor_level_count === 0) {
            $('.event_sponsor_right_header').hide();
            $('#continuePurchase').modal('hide');
            $('.get_started').hide();
        }
    }, 5000);
}
