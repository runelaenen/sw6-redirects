import './page/rl-redirects-list';
import './page/rl-redirects-details';
import './page/rl-redirects-create';

Shopware.Module.register('rl-redirects', {
    type: 'plugin',
    name: 'rl-redirects',
    title: 'rl-redirects.general.title',
    description: 'rl-redirects.general.title',
    color: '#189eff',
    icon: 'default-object-rocket',

    routes: {
        list: {
            component: 'rl-redirects-list',
            path: 'list',
        },
        details: {
            component: 'rl-redirects-details',
            path: 'details/:id',
            props: {
                default: (route) => {
                    return {
                        redirectId: route.params.id,
                    };
                },
            },
            meta: {
                parentPath: 'rl.redirects.list',
            },
        },
        create: {
            component: 'rl-redirects-create',
            path: 'create',
            meta: {
                parentPath: 'rl.redirects.list',
            },
        },
    },

    settingsItem: [{
        to: 'rl.redirects.list',
        group: 'shop',
        icon: 'small-arrow-large-double-right',
    }],
});
