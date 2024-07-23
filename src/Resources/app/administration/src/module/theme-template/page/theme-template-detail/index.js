import template from './theme-template-detail.html.twig';

Shopware.Component.register('theme-template-detail', {
    template,
    inject: [
        'repositoryFactory',
    ],
    props: {
        id: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            isLoading: true,
            template: null,
        }
    },
    computed: {
        templateRepository() {
            return this.repositoryFactory.create('theme_template');
        },
        isValidConfig() {
            return (
                this.template &&
                this.template.name &&
                this.template.label &&
                this.template.content
            );
        },
    },
    created() {
        this.onLoad();
    },
    methods: {
        async onLoad() {
            this.isLoading = true;

            this.template = await this.templateRepository.get(this.id);

            this.isLoading = false;
        },
        async onSave() {
            this.isLoading = true;

            await this.templateRepository.save(this.template);
            await this.onLoad();

            this.isLoading = false;
        },
    },
})
