<div class="container" id="luckyconsultation">
    <h1 class="display-1 text-center">Starte Anwendung&hellip;</h1>
</div>

<script type="text/javascript">
    window.LuckyConsultationPlugin = {
        API_URL    : '<?= PluginEngine::getURL('luckyconsultation', [], 'api', true) ?>',
        CID        : '<?= $course_id ?>',
        ICON_URL   : '<?= Assets::url('images/icons/') ?>',
        ASSETS_URL : '<?= Assets::url('') ?>',
        PLUGIN_ASSET_URL : '<?= $plugin->getAssetsUrl() ?>',
        ROUTE      : 'course'
    };
</script>

<!-- load bundles -->
<% for(var i = 0; i < htmlWebpackPlugin.tags.headTags.length; i++) { %>
    <% if (htmlWebpackPlugin.tags.headTags[i].attributes.rel) { %>
        <? PageLayout::addStylesheet($this->plugin->getPluginUrl() . '/static<%= htmlWebpackPlugin.tags.headTags[i].attributes.href %>'); ?>
    <% } else { %>
        <? PageLayout::addScript($this->plugin->getPluginUrl() . '/static<%= htmlWebpackPlugin.tags.headTags[i].attributes.src %>'); ?>
    <% } %>
<% } %>
<!-- END load bundles -->
