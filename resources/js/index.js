document.addEventListener('alpine:init', () => {
    Alpine.data(
        "grapesjs",
        ({ state, statePath, readOnly, tools, minHeight, container, plugins, settings, pluginsOpts }) => ({
            instance: null,
            state: state,
            tools: tools,
            plugins: plugins,
            settings: settings,
            pluginsOpts: pluginsOpts,
            init() {
                let enabledTools = {};
                let editorData = this.state.length > 0
                        ? JSON.parse(this.state)
                        : {},
                    projectData = editorData?.projectData ?? {};

                let theseSettings = {
                    height: minHeight + 'px',
                    container: container ? container : ".filament-grapesjs .grapesjs-wrapper",
                    showOffsets: true,
                    fromElement: false,
                    projectData: projectData,
                    noticeOnUnload: false,
                    storageManager: false,
                    plugins: plugins,
                    pluginsOpts: pluginsOpts,
                    // See https://github.com/GrapesJS/grapesjs/issues/4739#issuecomment-1336249292
                    selectorManager: {
                        escapeName: value => value
                    },
                    assetManager: {
                        upload: settings?.uploadConfig?.url || false, // Laravel API endpoint
                        uploadName: 'files', // The name of the file input
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        credentials: false, // Include cookies if needed for authentication
                        autoAdd: true, // Automatically add the uploaded image to the Asset Manager
                        assets: [], // Preload assets if needed
                    },
                    ...settings
                };

                this.instance =  grapesjs.init( theseSettings );
                this.instance.on('update', e => {

                    const component = this.instance.Pages.getSelected().getMainComponent();
                    let html = this.instance.getHtml({ component });
                    let extract = html.match(/<body\b[^>]*>([\s\S]*?)<\/body>/);
                    if( extract ) {
                        html = extract[1];
                    }
                    this.state = JSON.stringify({
                        'projectData': this.instance.getProjectData(),
                        'style': this.instance.getCss( { component }),
                        'html': html,
                    });
                })
            }
        })
    )
})
