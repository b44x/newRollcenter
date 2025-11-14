{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}

<div id="search-blog" class="block">
	<div class="block_content">
		<form method="post" id="form-search-blog" action="{Context::getContext()->link->getModuleLink('leoblog', 'search', array())|escape:'htmlall':'UTF-8'}">
			<input class=" form-control " type="text" id="search_blog" name="search_blog"  placeholder="{l s='Search...' mod='leoblog'}">
			<button type="submit" id="search_blog_button" class="btn btn-default button button-small"><i class="material-icons"></i></button>
		</form>
		<div id="blog-nav">
			<ul>
				<li>
					<a href="{Context::getContext()->link->getModuleLink('leoblog', 'search', array())|escape:'htmlall':'UTF-8'}?search_blog=Best_articles"><span class="sidebar-text">{l s='Best articles' mod='leoblog'}</span></a>
				</li>
				<li>
					<a href="{Context::getContext()->link->getModuleLink('leoblog', 'search', array())|escape:'htmlall':'UTF-8'}?search_blog=Latest_articles"><span class="sidebar-text">{l s='Latest articles' mod='leoblog'}</span></a>
				</li>
				<li>
					<a href="{Context::getContext()->link->getModuleLink('leoblog', 'search', array())|escape:'htmlall':'UTF-8'}?search_blog=Articles_favorite"><span class="sidebar-text">{l s='Articles favorite' mod='leoblog'}</span></a>
				</li>
			</ul>
		</div>
	</div>
</div>
