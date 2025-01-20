<x-filament-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>

<div class="panel__top">
    <div class="panel__basic-actions"></div>
</div>

    <div
            wire:ignore
            class="filament-grapesjs"
            data-dispatch="jsoneditor-loaded"
            x-on:jsoneditor-loaded-js.window="start"
            x-load-css="[
        @js(\Filament\Support\Facades\FilamentAsset::getStyleHref('grapesjs-css', 'dotswan/filament-grapesjs-v3')),
        @js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-grapesjs-css', 'dotswan/filament-grapesjs-v3'))
    ]"
            x-load-js="[
        @js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('grapesjs', 'dotswan/filament-grapesjs-v3')),
        @js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('filament-grapesjs-tailwindcss', 'dotswan/filament-grapesjs-v3')),
        @js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('filament-grapesjs', 'dotswan/filament-grapesjs-v3'))
    ]"
            x-data="{
			            editor: null,
			            destroy() {
			                this.editor = null;
			            },
			            start() {
			                $nextTick(() => {
			                    if(!this.editor) {
			                        const options = {}
			                        console.log('Starting GrapesJS', options)
			                        this.editor = grapesjs({
            container: '#gjs_{{$getId()}}',
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
            statePath: '{{ $getStatePath() }}',
            readOnly: {{ $isDisabled() ? 'true' : 'false' }},
            tools: @js($getTools()),
            plugins: @js($getPlugins()),
            pluginsOpts: @js($getPluginsOpts()),
            settings: @js($getSettings()),
            minHeight: @js($getMinHeight())
        })
			                    }
			                });
			            }
			        }"
    >

    <div
        id='gjs_{{$getId()}}'
        class="grapesjs-wrapper"
    >
        {!! $getHtmlData() !!}
    </div>

</div>

<div id="blocks"></div>
</x-filament-forms::field-wrapper>
