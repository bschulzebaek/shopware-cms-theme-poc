import template from './theme-template-list.html.twig';

Shopware.Component.register('theme-template-list', {
    template,
    inject: [
        'repositoryFactory'
    ],
    mixins: [
        Shopware.Mixin.getByName('listing'),
    ],
    data() {
        return {
            isLoading: true,
            templates: null,
            total: 0,
        }
    },
    computed: {
        templateRepository() {
            return this.repositoryFactory.create('theme_template');
        },
        templateColumns() {
            return [{
                property: 'name',
                label: 'Name',
                rawData: true,
                primary: true
            }, {
                property: 'label',
                label: 'Label',
            }, {
                property: 'content',
                label: 'Content',
                rawData: true
            }];
        },
        templateCriteria() {
            return new Shopware.Data.Criteria();
        },
    },
    methods: {
        async getList() {
            this.isLoading = true;

            const result = await this.templateRepository.search(this.templateCriteria);
            this.total = result.total;
            this.templates = result;
            this.isLoading = false;
        },
    },
})
