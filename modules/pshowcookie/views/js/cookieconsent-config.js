/* globals PSHOWCOOKIE__GROUPS, PSHOWCOOKIE__CONFIG, PSHOWCOOKIE__GCM */

(() => {
    const updateGcm = () => {
        if (!PSHOWCOOKIE__GCM.enabled) {
            return;
        }
        const check = (consent_id) => CookieConsent.acceptedCategory(consent_id) ? 'granted' : 'denied';
        gtag('consent', 'update', {
            ad_storage: check(PSHOWCOOKIE__GCM.ad_storage),
            ad_user_data: check(PSHOWCOOKIE__GCM.ad_user_data),
            ad_personalization: check(PSHOWCOOKIE__GCM.ad_personalization),
            analytics_storage: check(PSHOWCOOKIE__GCM.analytics_storage),
            personalization_storage: check(PSHOWCOOKIE__GCM.personalization_storage),
        });
    }

    let config = {
        autoShow: true,
        disablePageInteraction: PSHOWCOOKIE__CONFIG.disable_page_interaction,
        hideFromBots: PSHOWCOOKIE__CONFIG.hide_from_bots,
        mode: 'opt-in',
        revision: PSHOWCOOKIE__CONFIG.revision,
        cookie: {
            name: 'pshowcookie',
        },
        guiOptions: {
            consentModal: {
                layout: PSHOWCOOKIE__CONFIG.modal_layout,
                position: `${PSHOWCOOKIE__CONFIG.modal_position_y} ${PSHOWCOOKIE__CONFIG.modal_position_x}`,
                equalWeightButtons: false,
                flipButtons: false
            },
            preferencesModal: {
                layout: PSHOWCOOKIE__CONFIG.preferences_layout,
                position: PSHOWCOOKIE__CONFIG.preferences_position_x,
                equalWeightButtons: false,
                flipButtons: false
            }
        },

        onFirstConsent: ({cookie}) => {
            if (PSHOWCOOKIE__CONFIG.reload_on_change) {
                setTimeout(() => document.location.reload(), 500);
            }
        },

        onConsent: ({cookie}) => {
            /* updateGcm(); */
        },

        onChange: ({changedCategories, changedServices}) => {
            updateGcm();
            if (PSHOWCOOKIE__CONFIG.reload_on_change) {
                setTimeout(() => document.location.reload(), 500);
            }
        },

        categories: {},

        language: {
            default: '_',
            translations: {
                _: {
                    consentModal: {
                        title: PSHOWCOOKIE__CONFIG.modal_title,
                        description: PSHOWCOOKIE__CONFIG.modal_content,
                        acceptAllBtn: PSHOWCOOKIE__CONFIG.modal_btn_accept_all,
                        acceptNecessaryBtn: PSHOWCOOKIE__CONFIG.modal_btn_reject_all,
                        showPreferencesBtn: PSHOWCOOKIE__CONFIG.modal_btn_show_preferences,
                        // closeIconLabel: 'Reject all and close modal',
                        // footer: `
                        //     <a href="#path-to-impressum.html" target="_blank">Impressum</a>
                        //     <a href="#path-to-privacy-policy.html" target="_blank">Privacy Policy</a>
                        // `,
                    },
                    preferencesModal: {
                        title: PSHOWCOOKIE__CONFIG.preferences_title,
                        acceptAllBtn: PSHOWCOOKIE__CONFIG.preferences_btn_accept_all,
                        acceptNecessaryBtn: PSHOWCOOKIE__CONFIG.preferences_btn_reject_all,
                        savePreferencesBtn: PSHOWCOOKIE__CONFIG.preferences_btn_save,
                        closeIconLabel: PSHOWCOOKIE__CONFIG.preferences_btn_close,
                        sections: [
                            // {
                            //     title: 'Your Privacy Choices',
                            //     description: `In this panel you can express some preferences related to the processing of your personal information. You may review and change expressed choices at any time by resurfacing this panel via the provided link. To deny your consent to the specific processing activities described below, switch the toggles to off or use the “Reject all” button and confirm you want to save your choices.`,
                            // },
                            ...Object.values(PSHOWCOOKIE__GROUPS).map(group => ({
                                title: group.name,
                                description: group.description,
                                linkedCategory: group.reference,
                                cookieTable: {
                                    headers: {
                                        name: PSHOWCOOKIE__CONFIG.preferences_header_cookie,
                                        desc: PSHOWCOOKIE__CONFIG.preferences_header_desc
                                    },
                                    body: group.cookies.map(cookie => ({
                                        name: cookie.name,
                                        desc: cookie.description
                                    }))
                                }
                            })),
                            // {
                            //     title: 'More information',
                            //     description: 'For any queries in relation to my policy on cookies and your choices, please <a href="#contact-page">contact us</a>'
                            // }
                        ]
                    }
                }
            }
        }
    };

    for (let group of Object.values(PSHOWCOOKIE__GROUPS)) {
        config.categories[group.reference] = {
            enabled: group.required || group.default_granted,
            readOnly: group.required,
            autoClear: {
                cookies: [
                    ...group.cookies.map(cookie => {
                        let name = cookie.name;
                        if (name.startsWith('/')) {
                            const regexContent = cookie.name.replace(/^\/(.*)\/([a-z]+)?$/, '$1');
                            const regexOption = cookie.name.replace(/^\/(.*)\/([a-z]+)?$/, '$2');
                            name = new RegExp(regexContent, regexOption);
                        }
                        return {
                            'name': name
                        }
                    })
                ]
            }
        };
    }

    console.log(config);
    CookieConsent.run(config);
})();