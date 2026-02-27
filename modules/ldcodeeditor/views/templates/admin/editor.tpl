{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}
<prestashop-accounts></prestashop-accounts>
<div id="ps-billing"></div>
<div id="ps-modal"></div>
<div id="prestashop-cloudsync"></div>
<div id="ldce_wrapper" class="{if $darkMode == "false"}ldce_light{/if} {if not $ld_plan_active}disabled-form{/if}">
    <div class="ldce_loader"></div>
    <div class="ldce_main">
      <div class="ldce_filepicker">
        <div class="ldce_head">
          <div class="ldce_logo"></div>
          <div class="ldce_actions">
          <svg class="ldce_btn_fullscreen" style="width: 14px;margin-right: 5px;margin-top: 10px;" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg" fill="#FFF"><title/><g><path d="M30,0H6A5.9966,5.9966,0,0,0,0,6V30a6,6,0,0,0,12,0V12H30A6,6,0,0,0,30,0Z"/><path d="M90,0H66a6,6,0,0,0,0,12H84V30a6,6,0,0,0,12,0V6A5.9966,5.9966,0,0,0,90,0Z"/><path d="M30,84H12V66A6,6,0,0,0,0,66V90a5.9966,5.9966,0,0,0,6,6H30a6,6,0,0,0,0-12Z"/><path d="M90,60a5.9966,5.9966,0,0,0-6,6V84H66a6,6,0,0,0,0,12H90a5.9966,5.9966,0,0,0,6-6V66A5.9966,5.9966,0,0,0,90,60Z"/></g></svg>
            <svg class="ldce_btn_settings" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
              <path fill="#FFFFFF" d="M9.9,17.1l0.4,1.5c0.1,0.5,0.7,1,1.3,1h1c0.6,0,1.1-0.4,1.3-1l0.4-1.5c0.1-0.5,0.7-0.9,1.2-0.7l1.5,0.4
                c0.5,0.2,1.2-0.1,1.5-0.6l0.5-0.9c0.3-0.5,0.2-1.2-0.2-1.6l-1.1-1.1c-0.4-0.4-0.4-1,0-1.4l1.1-1.1c0.4-0.4,0.5-1.1,0.2-1.6l-0.5-0.9
                C18,7.3,17.4,7,16.8,7.2l-1.5,0.4c-0.5,0.2-1.1-0.2-1.2-0.7l-0.4-1.5c-0.1-0.5-0.7-1-1.3-1h-1c-0.6,0-1.1,0.4-1.3,1L9.9,6.9
                C9.7,7.4,9.2,7.8,8.7,7.6L7.2,7.2C6.6,7,6,7.3,5.7,7.8L5.2,8.7C4.9,9.1,5,9.8,5.4,10.2l1.1,1.1c0.4,0.4,0.4,1,0,1.4l-1.1,1.1
                c-0.4,0.4-0.5,1.1-0.2,1.6l0.5,0.9C6,16.7,6.6,17,7.2,16.8l1.5-0.4C9.2,16.2,9.7,16.6,9.9,17.1z M12,10c1.1,0,2,0.9,2,2
                c0,1.1-0.9,2-2,2s-2-0.9-2-2C10,10.9,10.9,10,12,10z"/>
            </svg>
            <svg class="ldce_btn_save disabled" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
              <path fill="#FFFFFF" class="st0" d="M17.7,4.4h-1.2v5.4h-4V4.4H6.3c-1,0-1.9,0.8-1.9,1.9v11.5c0,1,0.8,1.9,1.9,1.9h11.5c1,0,1.9-0.8,1.9-1.9V6.3
                C19.6,5.2,18.8,4.4,17.7,4.4z M17.5,17.5H6.5v-6.2h10.9V17.5z"/>
            </svg>
          </div>
        </div>
        <div class="files_wrapper"></div>
        <div class="settings_wrapper">
          <h2>SETTINGS</h2>
          <div class="form_field form_field_line">
            <label for="">Dark mode</label><input type="checkbox" class="switch_1" {if $darkMode != "false"}checked{/if} onchange="LDCE.switchMode()">
          </div>
          <div class="form_field">
            <label for="">Color scheme</label>
            <select onchange="LDCE.switchTheme(this.value)">
              <option {if $colorScheme == "default"}selected{/if} value="default">Default</option>
              <option {if $colorScheme == "3024-day"}selected{/if} value="3024-day">3024-day</option>
              <option {if $colorScheme == "3024-night"}selected{/if} value="3024-night">3024-night</option>
              <option {if $colorScheme == "abcdef"}selected{/if} value="abcdef">Abcdef</option>
              <option {if $colorScheme == "ambiance"}selected{/if} value="ambiance">Ambiance</option>
              <option {if $colorScheme == "ayu-dark"}selected{/if} value="ayu-dark">Ayu-dark</option>
              <option {if $colorScheme == "ayu-mirage"}selected{/if} value="ayu-mirage">Ayu-mirage</option>
              <option {if $colorScheme == "base16-dark"}selected{/if} value="base16-dark">Base16-dark</option>
              <option {if $colorScheme == "base16-light"}selected{/if} value="base16-light">Base16-light</option>
              <option {if $colorScheme == "bespin"}selected{/if} value="bespin">Bespin</option>
              <option {if $colorScheme == "blackboard"}selected{/if} value="blackboard">Blackboard</option>
              <option {if $colorScheme == "cobalt"}selected{/if} value="cobalt">Cobalt</option>
              <option {if $colorScheme == "colorforth"}selected{/if} value="colorforth">Colorforth</option>
              <option {if $colorScheme == "darcula"}selected{/if} value="darcula">Darcula</option>
              <option {if $colorScheme == "duotone-dark"}selected{/if} value="duotone-dark">Duotone-dark</option>
              <option {if $colorScheme == "duotone-light"}selected{/if} value="duotone-light">Duotone-light</option>
              <option {if $colorScheme == "eclipse"}selected{/if} value="eclipse">Eclipse</option>
              <option {if $colorScheme == "elegant"}selected{/if} value="elegant">Elegant</option>
              <option {if $colorScheme == "erlang-dark"}selected{/if} value="erlang-dark">Erlang-dark</option>
              <option {if $colorScheme == "gruvbox-dark"}selected{/if} value="gruvbox-dark">Gruvbox-dark</option>
              <option {if $colorScheme == "hopscotch"}selected{/if} value="hopscotch">Hopscotch</option>
              <option {if $colorScheme == "icecoder"}selected{/if} value="icecoder">Icecoder</option>
              <option {if $colorScheme == "idea"}selected{/if} value="idea">Idea</option>
              <option {if $colorScheme == "isotope"}selected{/if} value="isotope">Isotope</option>
              <option {if $colorScheme == "lesser-dark"}selected{/if} value="lesser-dark">Lesser-dark</option>
              <option {if $colorScheme == "liquibyte"}selected{/if} value="liquibyte">Liquibyte</option>
              <option {if $colorScheme == "lucario"}selected{/if} value="lucario">Lucario</option>
              <option {if $colorScheme == "material"}selected{/if} value="material">Material</option>
              <option {if $colorScheme == "material-darker"}selected{/if} value="material-darker">Material-darker</option>
              <option {if $colorScheme == "material-palenight"}selected{/if} value="material-palenight">Material-palenight</option>
              <option {if $colorScheme == "material-ocean"}selected{/if} value="material-ocean">Material-ocean</option>
              <option {if $colorScheme == "mbo"}selected{/if} value="mbo">Mbo</option>
              <option {if $colorScheme == "mdn-like"}selected{/if} value="mdn-like">Mdn-like</option>
              <option {if $colorScheme == "midnight"}selected{/if} value="midnight">Midnight</option>
              <option {if $colorScheme == "monokai"}selected{/if} value="monokai">Monokai</option>
              <option {if $colorScheme == "monokaipro"}selected{/if} value="monokaipro">monokaipro</option>
              <option {if $colorScheme == "moxer"}selected{/if} value="moxer">Moxer</option>
              <option {if $colorScheme == "neat"}selected{/if} value="neat">Neat</option>
              <option {if $colorScheme == "neo"}selected{/if} value="neo">Neo</option>
              <option {if $colorScheme == "night"}selected{/if} value="night">Night</option>
              <option {if $colorScheme == "nord"}selected{/if} value="nord">Nord</option>
              <option {if $colorScheme == "oceanic-next"}selected{/if} value="oceanic-next">Oceanic-next</option>
              <option {if $colorScheme == "panda-syntax"}selected{/if} value="panda-syntax">Panda-syntax</option>
              <option {if $colorScheme == "paraiso-dark"}selected{/if} value="paraiso-dark">Paraiso-dark</option>
              <option {if $colorScheme == "paraiso-light"}selected{/if} value="paraiso-light">Paraiso-light</option>
              <option {if $colorScheme == "pastel-on-dark"}selected{/if} value="pastel-on-dark">Pastel-on-dark</option>
              <option {if $colorScheme == "railscasts"}selected{/if} value="railscasts">Railscasts</option>
              <option {if $colorScheme == "rubyblue"}selected{/if} value="rubyblue">Rubyblue</option>
              <option {if $colorScheme == "seti"}selected{/if} value="seti">Seti</option>
              <option {if $colorScheme == "shadowfox"}selected{/if} value="shadowfox">Shadowfox</option>
              <option {if $colorScheme == "solarized dark"}selected{/if} value="solarized dark">Solarized dark</option>
              <option {if $colorScheme == "solarized light"}selected{/if} value="solarized light">Solarized light</option>
              <option {if $colorScheme == "the-matrix"}selected{/if} value="the-matrix">The-matrix</option>
              <option {if $colorScheme == "tomorrow-night-bright"}selected{/if} value="tomorrow-night-bright">Tomorrow-night-bright</option>
              <option {if $colorScheme == "tomorrow-night-eighties"}selected{/if} value="tomorrow-night-eighties">Tomorrow-night-eighties</option>
              <option {if $colorScheme == "ttcn"}selected{/if} value="ttcn">Ttcn</option>
              <option {if $colorScheme == "twilight"}selected{/if} value="twilight">Twilight</option>
              <option {if $colorScheme == "vibrant-ink"}selected{/if} value="vibrant-ink">Vibrant-ink</option>
              <option {if $colorScheme == "xq-dark"}selected{/if} value="xq-dark">Xq-dark</option>
              <option {if $colorScheme == "xq-light"}selected{/if} value="xq-light">Xq-light</option>
              <option {if $colorScheme == "yeti"}selected{/if} value="yeti">Yeti</option>
              <option {if $colorScheme == "yonce"}selected{/if} value="yonce">Yonce</option>
              <option {if $colorScheme == "zenburn"}selected{/if} value="zenburn">Zenburn</option>
            </select>
          </div>
        </div>
      </div>
      <div class="ldce_spliter"></div>
      <div class="ldce_editor">
        <div class="ldce_empty">
          <svg width="300" style="max-width: 300px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1063.3 937.1" fill="#f2f2f2"><path d="M567.9 572.7c0-125.6 101.8-227.4 227.4-227.4 66.6 0 127.1 29.1 169.2 75V183.8c0-39.8-32.2-72-72-72H525.8c-22.2 0-40.6-17.6-40.6-40.6v.8c0-39.8-32.2-72-72-72H155.3c-40.6.8-72 32.9-72 72v609.4c0 39.8 32.2 72 72 72h500c-53.7-42.9-87.4-108-87.4-180.7zm371.4 89.6c15.3-25.3 23.7-56.7 23.7-88 0-95.7-75.8-173-170-173s-169.2 78.1-169.2 173.8 75.8 173 170 173c35.2 0 68.1-10.7 95.7-29.9l121.7 124 52.1-52.8-124-127.1zm-145.5 29.8c-64.3 0-116.4-52.8-116.4-117.9s52.1-117.9 116.4-117.9 116.4 52.8 116.4 117.9-52.1 117.9-116.4 117.9zm-679.1 240l4-8.8c4.2 3.1 10.6 5.3 16.6 5.3 6.9 0 9.7-2.3 9.7-5.4 0-9.4-29.3-3-29.3-21.7 0-8.6 6.9-15.7 21.2-15.7 6.3 0 12.8 1.5 17.5 4.5l-3.6 8.9c-4.7-2.7-9.5-4-14-4-6.9 0-9.6 2.6-9.6 5.8 0 9.3 29.3 2.9 29.3 21.4 0 8.4-7 15.6-21.4 15.6-7.9 0-16-2.4-20.4-5.9zm88.9-4.4v9.4h-39v-50.4h38.1v9.4h-26.5V907h23.4v9.1h-23.4v11.7h27.4zm9.3-41h11.7v40.9h25.3v9.5H213v-50.4zm82.5 41v9.4h-39v-50.4h38.1v9.4H268V907h23.4v9.1H268v11.7h27.4zm6-15.8c0-15.2 11.7-26.1 27.4-26.1 8.7 0 16 3.2 20.7 8.9l-7.5 6.9c-3.4-3.9-7.6-5.9-12.7-5.9-9.4 0-16.1 6.6-16.1 16.1s6.7 16.1 16.1 16.1c5 0 9.3-2 12.7-6l7.5 6.9c-4.8 5.8-12 9-20.8 9-15.7.2-27.3-10.7-27.3-25.9zm65.9-15.7h-16.1v-9.5h43.9v9.5H379v40.9h-11.7v-40.9zm66.1-.1v13.3h23.3v9.4h-23.3v18.4h-11.7v-50.4h38.1v9.4h-26.4zm34.3-9.4h11.7v50.4h-11.7v-50.4zm23.6 0H503v40.9h25.3v9.5h-36.9v-50.4zm82.5 41v9.4h-39v-50.4h38.1v9.4h-26.5V907h23.4v9.1h-23.4v11.7h27.4zm5.3 4.4l4-8.8c4.2 3.1 10.6 5.3 16.6 5.3 6.9 0 9.7-2.3 9.7-5.4 0-9.4-29.3-3-29.3-21.7 0-8.6 6.9-15.7 21.2-15.7 6.3 0 12.8 1.5 17.5 4.5l-3.6 8.9c-4.7-2.7-9.5-4-14-4-6.9 0-9.6 2.6-9.6 5.8 0 9.3 29.3 2.9 29.3 21.4 0 8.4-7 15.6-21.4 15.6-7.8 0-15.9-2.4-20.4-5.9zm80.8-35.9h-16.1v-9.5h43.9v9.5h-16.1v40.9h-11.7v-40.9zm30.1 15.7c0-15 11.7-26.1 27.6-26.1 15.8 0 27.6 11 27.6 26.1 0 15-11.7 26.1-27.6 26.1S690 926.9 690 911.9zm43.4 0c0-9.6-6.8-16.1-15.8-16.1s-15.8 6.6-15.8 16.1 6.8 16.1 15.8 16.1 15.8-6.5 15.8-16.1zm80 15.8v9.4h-39v-50.4h38.1v9.4H786V907h23.4v9.1H786v11.7h27.4zm9.2-41h22.9c16.5 0 27.8 9.9 27.8 25.2s-11.3 25.2-27.8 25.2h-22.9v-50.4zm22.4 40.8c10 0 16.6-6 16.6-15.6s-6.6-15.6-16.6-15.6h-10.7v31.3H845zm37.1-40.8h11.7v50.4h-11.7v-50.4zm34.1 9.5H900v-9.5h44v9.5h-16.1v40.9h-11.7v-40.9z"/></svg>
        </div>
        <div class="ldce_bar">
        </div>
        <div class="ldce_editor_cm"></div>
      </div>
    </div>
  </div>
  <script>
    LDCE_OPTIONS = {
      theme: "{$colorScheme|escape:'javascript':'UTF-8'}",
      darkMode: {if $darkMode != "false"}true{else}false{/if},
      url: "{$link->getModuleLink('ldcodeeditor', 'ajax.php', array())|escape:'javascript':'UTF-8'}",
      token: "{Tools::getAdminTokenLite('AdminCodeeditor')}"
    }
  </script>

  
<script src="{$urlAccountsCdn|escape:'htmlall':'UTF-8'}" rel=preload></script>
<script src="{$urlBilling|escape:'htmlall':'UTF-8'}" rel=preload></script>
<script src="{$urlCloudsync|escape:'htmlall':'UTF-8'}"></script>

<script>
	/*********************
	 * PrestaShop Account *
	 * *******************/
	window.LDCODEEDITOR = {
		psAccount: false,
		psCloudsync: false,
	};
	window?.psaccountsVue?.init();

  	  // CloudSync
  	  const cdc = window.cloudSyncSharingConsent;

      cdc.init('#prestashop-cloudsync');
      cdc.on('OnboardingCompleted', (isCompleted) => {
        window.location.reload()
      });

	if (window.psaccountsVue.isOnboardingCompleted() == true) {
		window.LDCODEEDITOR.psAccount = true;
		/*********************
		 * PrestaShop Billing *
		 * *******************/
		window.psBilling.initialize(window.psBillingContext.context, '#ps-billing', '#ps-modal', (type, data) => {
			// Event hook listener
			switch (type) {
				// Hook triggered when the subscription is created
				case window.psBilling.EVENT_HOOK_TYPE.SUBSCRIPTION_CREATED:
					window.location.reload()
					break;
					// Hook when the subscription is updated
				case window.psBilling.EVENT_HOOK_TYPE.SUBSCRIPTION_UPDATED:
					window.location.reload()
					break;
					// Hook triggered when the subscription is cancelled
				case window.psBilling.EVENT_HOOK_TYPE.SUBSCRIPTION_CANCELLED:
					window.location.reload()
					break;
			}
		});
	}
	function refreshLdConfig(){
		if(window.LDCODEEDITOR.psCloudsync && window.LDCODEEDITOR.psAccount){
			document.querySelector("#ldce_wrapper").classList.remove("soft-disabled-form");
		} else {
			document.querySelector("#ldce_wrapper").classList.add("soft-disabled-form");
		}
	}
</script>