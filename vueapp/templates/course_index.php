<div class="container" id="luckyconsultation">
    <h1 class="display-1 text-center">Starte Anwendung&hellip;</h1>
</div>

<script type="text/javascript">
    let API_URL  = '<?= PluginEngine::getURL('luckyconsultation', [], 'api', true) ?>';
    let CID      = '<?= $course_id ?>';
    let ICON_URL = '<?= Assets::url('images/icons/') ?>';
    let ASSETS_URL = '<?= Assets::url('') ?>';
    let PLUGIN_ASSET_URL =  '<?= $plugin->getAssetsUrl() ?>';
</script>

<!-- load bundles -->
<% for(var i = 0; i < htmlWebpackPlugin.tags.headTags.length; i++) { %>
<? PageLayout::addScript($this->plugin->getPluginUrl() . '/static<%= htmlWebpackPlugin.tags.headTags[i].attributes.src %>'); ?>
<% } %>
<!-- END load bundles -->
