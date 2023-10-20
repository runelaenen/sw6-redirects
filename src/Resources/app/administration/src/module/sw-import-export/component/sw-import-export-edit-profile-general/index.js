export default {
    computed: {
        supportedEntities() {
            const supportedEntities = this.$super('supportedEntities');

            supportedEntities.push({
                value: 'rl_redirects_redirect',
                label: this.$tc('sw-import-export.profile.rlRedirectLabel'),
                type: 'import-export',
            });

            return supportedEntities;
        },
    },

};
