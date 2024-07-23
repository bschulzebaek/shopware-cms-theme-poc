import template from './theme-template-create.html.twig';

Shopware.Component.register('theme-template-create', {
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
        this.onCreate();
    },
    methods: {
        onCreate() {
            this.template = this.templateRepository.create();
            this.template.active = true;

            this.isLoading = false;
        },
        async onSave() {
            this.isLoading = true;

            try {
                await this.templateRepository.save(this.template);
                this.$router.push({
                    name: 'theme.template.detail',
                    params: {
                        id: this.template.id
                    },
                });
            } catch(e) {
                console.error(e);
            }

            this.isLoading = false;
        },
    },
})
