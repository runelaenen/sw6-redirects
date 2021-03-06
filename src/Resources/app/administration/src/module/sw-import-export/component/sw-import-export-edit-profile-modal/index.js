
Shopware.Component.override('sw-import-export-edit-profile-modal', {
    created() {
        this.supportedEntities.push({
            value: 'rl_redirects_redirect',
            label: this.$tc('sw-import-export.profile.rlRedirectLabel')
        });

        console.log(this.supportedEntities);
    }
});
