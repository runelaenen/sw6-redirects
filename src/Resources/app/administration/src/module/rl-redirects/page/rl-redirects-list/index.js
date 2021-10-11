import template from './rl-redirects-list.html.twig';

const { Component } = Shopware;
const Criteria = Shopware.Data.Criteria;

Component.register('rl-redirects-list', {
    template,

    inject: [
        'repositoryFactory',
    ],

    data() {
        return {
            redirects: null,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    computed: {
        columns() {
            return [{
                property: 'source',
                dataIndex: 'source',
                label: this.$tc('rl-redirects.list.columnSourceUrl'),
                routerLink: 'rl.redirects.details',
                inlineEdit: 'string',
                allowResize: true,
                primary: true,
            }, {
                property: 'target',
                dataIndex: 'target',
                label: this.$tc('rl-redirects.list.columnTargetUrl'),
                inlineEdit: 'string',
                allowResize: true,
            }, {
                property: 'httpCode',
                dataIndex: 'httpCode',
                label: this.$tc('rl-redirects.list.columnHttpCode'),
                allowResize: true,
            }];
        },
        redirectRepository() {
            return this.repositoryFactory.create('rl_redirects_redirect');
        },
    },

    created() {
        this.redirectRepository.search(new Criteria(), Shopware.Context.api).then((result) => {
            this.redirects = result;
        });
    },

});
