<div class="modal sponsorship-option fade" id="upgradeModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="upgradeModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg bg-white" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white" style="border: none;">
                <div class="w-100">
                    <button type="button" class="btn pull-right" data-dismiss="modal" aria-label="Close">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z"
                                  fill="#D3D3D3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="modal-body">
                <div id="upgradeCards" class="d-flex">
                    <!-- Upgrade cards will be dynamically inserted here -->
                </div>
            </div>

            <div class="modal-footer py-0" style="border: none;">
                <svg class="my-0" width="347" viewBox="0 0 347 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="174.5" cy="55.5" r="55.5" fill="#C0C0C0"/>
                    <circle cx="291.5" cy="55.5" r="55.5" fill="#FFC107"/>
                    <circle cx="55.5" cy="56.5" r="55.5" fill="#FFC97D"/>
                    <circle cx="174" cy="56" r="27" fill="#F0F3F3"/>
                    <circle cx="291" cy="56" r="27" fill="#FFEA8F"/>
                    <circle cx="55" cy="57" r="27" fill="#FFE6C3"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<?php
$um_encodeCurrentUrl = encrypt_url_safe(getCurrentUrl());
?>

<script type="application/javascript">
    const um_encodeCurrentUrl = "<?php echo $um_encodeCurrentUrl ?? ''; ?>";

    async function constructUpgradeModalContent(event_output, my_pToken, rsvp_slug, isLoggedIn, social_token = '', trackingtoken = '') {
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

        let sponsor_model_data = '';
        let sponsor_level_count = 0;

        const rsvpTicket = rsvp_slug ? (eventTicketTypes.find(t => t.slug === rsvp_slug) || {}) : {};

        $.each(eventSponsorWidgetType, function(k, widget) {
            if (!isValidProfileForSponsor(widget, eventTicketTypes, eventSponsorWidgetType, rsvpTicket, my_profile_type, isLoggedIn)) return true;

            let slug = widget.slug;
            let { charging_price, price, discount_percentage } = calculateTicketPricing(widget, 2, eventTicketTypes, is_social_share_enabled);

            // let total_used = calculateTotalUsed(sponsorGroup, widget.title);
            let full_content = generateFullContent(widget);

            let social_param = social_token ? `/socialshare/${social_token}` : '';
            let shareUrl = trackingtoken ? updateShareUrl(trackingtoken, slug) : '';

            let upgradeLink = `${_taoh_site_url_root}/events/event_sponsor/${eventtoken}/${widget.slug}${social_param}`;
            if(rsvp_slug){
                let ticketType = eventTicketTypes.find(ticket => ticket.title === widget.award_ticket_type) || {};
                upgradeLink = `${_taoh_site_url_root}/events/upgrade_rsvp/${eventtoken}/${encodeURIComponent(ticketType?.title)}/${um_encodeCurrentUrl}`;
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

        // handleEmptySponsors(sponsor_level_count);
    }

    function constructSponsorInfoPopup(eventtoken, eventSponsorWidgetType, user_profile_type = '', org_email = '', social_token = '', eventTicketTypes, event_form_version, is_social_share_enabled = 0, trackingtoken = '', isLoggedIn = '') {
        const evenKey = `event_MetaInfo_${eventtoken}`;

        let rsvp_slug = '';

        IntaoDB.getItem(objStores.event_store.name, evenKey).then((data) => {
            if (data?.values) {
                const sponsor_data = data.values.output;
                const sponsorGroup = sponsor_data.event_sponsor || {};
                constructBoxForSponsor(rsvp_slug, eventSponsorWidgetType, sponsorGroup, user_profile_type, org_email, social_token, eventTicketTypes, event_form_version, is_social_share_enabled, trackingtoken, isLoggedIn);
            } else {
                constructBoxForSponsor(rsvp_slug, eventSponsorWidgetType, {}, user_profile_type, org_email, social_token, eventTicketTypes, event_form_version, is_social_share_enabled, trackingtoken, isLoggedIn);
            }
        });
    }

    function constructBoxForSponsor(rsvp_slug, eventSponsorWidgetType, sponsorGroup, user_profile_type = '', organizerEmail = '', social_token = '', eventTicketTypes, event_form_version, is_social_share_enabled = 0, trackingtoken = '', isLoggedIn = '') {
        let sponsor_model_data = '';

        if (Array.isArray(eventSponsorWidgetType)) {
            sponsor_model_data += `
        <div class="modal-dialog bg-white" role="document">
            <div class="modal-content">
                <div class="modal-header bg-white" style="border: none;">
                    <h5 class="modal-title text-center mt-4 heading" id="myModalLabel" style="flex: 1;"></h5>
                    <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z" fill="#D3D3D3"/>
                        </svg>
                    </button>
                </div>
                <div class="modal-body d-flex mx-auto" style="gap: 24px;">
        `;

            let sponsor_level_count = 0;

            const rsvpTicket = rsvp_slug ? (eventTicketTypes.find(t => t.slug === rsvp_slug) || {}) : {};

            $.each(eventSponsorWidgetType, function(k, widget) {
                if (!isValidProfileForSponsor(widget, eventTicketTypes, {}, rsvpTicket, user_profile_type, isLoggedIn)) return true;

                let slug = widget.slug;
                let { charging_price, price, discount_percentage } = calculateTicketPricing(widget, event_form_version, eventTicketTypes, is_social_share_enabled);

                // let total_used = calculateTotalUsed(sponsorGroup, widget.title);
                let full_content = generateFullContent(widget);

                let social_param = social_token ? `/socialshare/${social_token}` : '';
                let shareUrl = trackingtoken ? updateShareUrl(trackingtoken, slug) : '';

                let upgradeLink = `${_taoh_site_url_root}/events/event_sponsor/${eventtoken}/${widget.slug}${social_param}`;

                sponsor_model_data += generateSponsorHTML(widget, charging_price, price, full_content, isLoggedIn, '', upgradeLink, organizerEmail, is_social_share_enabled, discount_percentage, shareUrl);
                sponsor_level_count++;
            });

            sponsor_model_data += `</div></div></div></div>`;

            $('#sponsorInfo').html(sponsor_model_data);
            handleEmptySponsors(sponsor_level_count);
        }
    }

    function isValidProfileForSponsor(widget, eventTicketTypes, eventSponsorWidgetType, rsvpTicket, user_profile_type, isLoggedIn) {
        const awardType = widget?.award_ticket_type;
        if (!awardType) return false;

        if (rsvpTicket?.slug) {
            // If user's current ticket type equals award ticket type → not valid
            if (taoh_title_desc_decode(rsvpTicket.title) === awardType) return false;

            const rsvp_slug = rsvpTicket.slug;

            return eventTicketTypes.some(t => {
                const applicable = t.applicable_to || [];
                if (!(applicable.includes('all') || applicable.includes(rsvp_slug))) return false;
                return taoh_title_desc_decode(t.title) === awardType;
            });
        } else {
            // For users who are not logged in
            if (!isLoggedIn) {
                if (Array.isArray(widget.profiletype) && !widget.profiletype.includes('visitor') && !widget.profiletype.includes('all')) {
                    return false;
                }
            } else {
                // For logged-in users
                if (Array.isArray(widget.profiletype) && !widget.profiletype.includes(user_profile_type) && !widget.profiletype.includes('all')) {
                    return false;
                }
            }
        }

        return true;
    }

    function calculateTicketPricing(widget, event_form_version, eventTicketTypes, is_social_share_enabled) {
        let charging_price = 0, price = 0, discount_percentage = 0;

        if (event_form_version == 2) {
            let TicketArr = eventTicketTypes.find(ticket => ticket.title === widget.award_ticket_type) || {};
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

        return { charging_price, price, discount_percentage };
    }

    function calculateTotalUsed(sponsorGroup, title) {
        let total_used = 0;
        sponsorGroup.forEach(value => {
            if ((value.sponsor_type)?.toLowerCase() == title?.toLowerCase()) total_used++;
        });

        return total_used;
    }

    function generateFullContent(widget) {
        let full_content = '';
        if (Array.isArray(widget.incentives)) {
            widget.incentives.forEach(m => {
                if (m) {
                    full_content += `<li class="py-2"><p><span>${m}</span></p></li>`;
                }
            });
        }
        return full_content;
    }

    function generateSponsorHTML(widget, charging_price, price, full_content, isLoggedIn, rsvp_slug, upgradeLink, organizerEmail, is_social_share_enabled, discount_percentage, shareUrl) {
        const isUpgrade = !!rsvp_slug;

        return `
        <div class="gold-card py-3">
            <div>
                <h6 class="label py-2 px-2">${widget.title} ${widget.recommended == 1 ? `<p class="recommended-tag">RECOMMENDED</p>` : '<p class="recommended-tag">&nbsp;</p>'} </h6>
                <p class="event-price-tag py-2 mt-4 px-2">$${charging_price} ${charging_price != price ? `(<span style="text-decoration: line-through;">$${price}</span>)` : ''} Per Event</p>
                <ul class="px-3">${full_content || `<li class="py-2"><p><span>${widget.title} Branding on Event Pages</span></p></li>`}</ul>
            </div>

            <div>
                <div class="d-flex px-3 ${!isLoggedIn ? 'flex-wrap' : ''}" style="gap: 4px;">
                    <div class="${!isLoggedIn ? 'flex-grow-1' : ''}" style="display:${(widget?.get_started !== undefined && widget.get_started != 1) ? 'none' : 'block'}">
                        ${isLoggedIn ? `<a class="btn ${!isUpgrade ? 'get-started' : ''} upgrade-btn w-100" href="${upgradeLink}">${charging_price > 0 ? 'Buy Now' : 'Claim Now!'}</a>` :
                `<button type="button" class="login-and-buy mt-3 mb-2 btn w-100 create_referral" data-location="${upgradeLink}" data-title="${btoa(unescape(encodeURIComponent(widget.title)))}" data-toggle="modal" data-target="#config-modal" data-dismiss="modal"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>${charging_price > 0 ? 'Login & Buy Now' : 'Login & Claim Now!'}</button>`}
                    </div>
                    ${organizerEmail
                ? `<div class="flex-grow-1"><a class="btn ${!isUpgrade ? 'get-started' : ''} contact-us upgrade-contact-us-btn w-100" href="#" data-toggle="modal" data-target="#contactusModal" data-email="${organizerEmail}" data-dismiss="modal">Contact us</a></div>`
                : ''}
                </div>

                ${isUpgrade ? `<ul class="d-flex px-3 mt-2 svg-styled">
                    <li>
                        <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.2923 0.0256658C9.82275 0.788638 6.88018 4.67069 4.05964 8.65749C2.86259 6.64193 1.59444 5.96894 0.283202 6.65849C0.121354 6.74348 0.0147307 6.90646 0.00139086 7.08976C-0.0119378 7.27495 0.0712083 7.4538 0.221637 7.56353C1.17242 8.25737 2.15876 9.4872 3.33173 11.4342V11.4336C3.54118 11.7869 3.922 12.0025 4.33201 12H4.35105C4.77376 11.9962 5.16093 11.7653 5.36593 11.3962C6.50712 9.32866 7.76061 7.32576 9.12076 5.39576C10.2353 3.81273 11.4781 2.3229 12.8363 0.942167C13.0191 0.763309 13.0527 0.481724 12.9175 0.264808C12.7912 0.0485417 12.531 -0.0510666 12.2923 0.0256658Z" fill="#3DB057"/>
                        </svg>
                        <span class="ml-1">Your previous payment will be refunded upon upgrading to this option.</span>
                    </li>
                </ul>` : ''}


                <div class="pt-3" style="${is_social_share_enabled && discount_percentage > 0 ? '' : 'display:none'}">
                    <ul class="d-flex px-3 svg-styled" style="gap: 4px;">
                        <li><svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.2923 0.0256658C9.82275 0.788638 6.88018 4.67069 4.05964 8.65749C2.86259 6.64193 1.59444 5.96894 0.283202 6.65849C0.121354 6.74348 0.0147307 6.90646 0.00139086 7.08976C-0.0119378 7.27495 0.0712083 7.4538 0.221637 7.56353C1.17242 8.25737 2.15876 9.4872 3.33173 11.4342V11.4336C3.54118 11.7869 3.922 12.0025 4.33201 12H4.35105C4.77376 11.9962 5.16093 11.7653 5.36593 11.3962C6.50712 9.32866 7.76061 7.32576 9.12076 5.39576C10.2353 3.81273 11.4781 2.3229 12.8363 0.942167C13.0191 0.763309 13.0527 0.481724 12.9175 0.264808C12.7912 0.0485417 12.531 -0.0510666 12.2923 0.0256658Z" fill="#3DB057"/></svg>
                        <span class="ml-1">Share and get ${discount_percentage}% discount for this ticket</span></li>
                    </ul>
                    <div class="social_sharing d-flex px-3 pt-3" style="gap: 4px;">
                        <button class="btn get-started upgrade-btn sponsor-share-click" data-toggle="modal" data-url="${shareUrl}" data-target="#shareModal" data-dismiss="modal" aria-label="Close">Share for ${discount_percentage}% Off</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    }

    function updateShareUrl(trackingtoken, slug) {
        let shareUrl = $("#share_link").val();
        return shareUrl.replace(`${trackingtoken}/stlo`, `${trackingtoken}/${slug}/stlo`);
    }

    function handleEmptySponsors(sponsor_level_count) {
        setTimeout(() => {
            if (sponsor_level_count === 0) {
                $('.event_sponsor_right_header').hide();
                $('#continuePurchase').modal('hide');
                $('.get_started').hide();
            }
        }, 5000);
    }
</script>