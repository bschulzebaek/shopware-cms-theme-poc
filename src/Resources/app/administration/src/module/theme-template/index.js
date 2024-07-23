import './page/theme-template-create';
import './page/theme-template-detail';
import './page/theme-template-list';

Shopware.Module.register('theme-template', {
    type: 'plugin',
    name: 'Theme Templates',
    title: 'Theme Templates',
    description: 'sw-property.general.descriptionTextModule',
    color: '#ff3d58',
    icon: 'regular-pencil-s',

    routes: {
        list: {
            component: 'theme-template-list',
            path: 'list'
        },
        detail: {
            component: 'theme-template-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'theme.template.list'
            },
            props: {
                default(route) {
                    return {
                        id: route.params.id,
                    };
                },
            },
        },
        create: {
            component: 'theme-template-create',
            path: 'create',
            meta: {
                parentPath: 'theme.template.list'
            },
        },
    },

    navigation: [{
        label: 'Theme Templates',
        color: '#ff3d58',
        path: 'theme.template.list',
        icon: 'regular-pencil-s',
        position: 100,
        parent: 'sw-content',
    }],
});
