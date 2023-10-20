import template from './rl-redirects-details.html.twig';

const { Mixin } = Shopware;

export default {
    template,

    inject: [
        'repositoryFactory',
    ],

    mixins: [
        Mixin.getByName('notification'),
    ],

    props: {
        redirectId: {
            type: String,
            required: false,
            default: null,
        },
    },

    data() {
        return {
            redirect: null,
            isLoading: true,
            processSuccess: false,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    computed: {
        redirectRepository() {
            return this.repositoryFactory.create('rl_redirects_redirect');
        },
    },

    created() {
        this.getRedirect();
    },

    methods: {
        getRedirect() {
            this.isLoading = true;
            this.redirectRepository.get(
                this.redirectId,
                Shopware.Context.api,
            ).then((entity) => {
                this.redirect = entity;
                this.isLoading = false;
            });
        },

        onClickSave() {
            if (this.redirect.source === this.redirect.target) {
                this.createNotificationError({
                    title: this.$tc('rl-redirects.detail.errorTitle'),
                    message: this.$tc('rl-redirects.detail.errorSameUrlDescription'),
                });
                return;
            }

            this.isLoading = true;
            this.redirectRepository.save(this.redirect, Shopware.Context.api).then(() => {
                this.getRedirect();

                this.isLoading = false;
                this.processSuccess = true;
            }).catch((exception) => {
                this.isLoading = false;

                this.createNotificationError({
                    title: this.$tc('rl-redirects.detail.errorTitle'),
                    message: exception,
                });
            });
        },

        saveFinish() {
            this.processSuccess = false;
        },
    },

};
