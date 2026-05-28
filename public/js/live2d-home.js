(function () {
    'use strict';

    const widget = document.getElementById('live2d-widget');
    const canvas = document.getElementById('live2d-canvas');

    if (!widget || !canvas) {
        return;
    }

    const modelSrc = widget.dataset.modelSrc;
    const vtubeSrc = widget.dataset.vtubeSrc;
    const live2dRuntime = window.PIXI && window.PIXI.live2d;
    const hasRuntime = live2dRuntime && live2dRuntime.Live2DModel && window.Live2DCubismCore;

    if (!modelSrc || !hasRuntime) {
        widget.classList.add('is-hidden');
        console.warn('Live2D runtime atau model tidak ditemukan.');
        return;
    }

    const Live2DModel = live2dRuntime.Live2DModel;
    const MotionPriority = live2dRuntime.MotionPriority || {
        IDLE: 1,
        FORCE: 3,
    };
    let app = null;
    let model = null;
    let idleTimer = null;
    let vtubeSettings = null;
    let vbridgerTicker = null;
    const safeVBridgerParams = [
        'ParamMouthForm',
        'ParamMouthOpenY',
        'Param2',
        'Param3',
        'Param4',
        'Param6',
        'Param8',
        'Param9',
    ];

    function createApp() {
        app = new window.PIXI.Application({
            view: canvas,
            resizeTo: widget,
            transparent: true,
            backgroundAlpha: 0,
            antialias: true,
            autoDensity: true,
            resolution: Math.min(window.devicePixelRatio || 1, 2),
        });

        app.renderer.plugins.interaction.autoPreventDefault = false;
    }

    function fitModel() {
        if (!app || !model) {
            return;
        }

        const baseWidth = model.internalModel.width || model.width || 1;
        const baseHeight = model.internalModel.height || model.height || 1;
        const scale = Math.min(
            (app.screen.width / baseWidth) * 1.18,
            (app.screen.height / baseHeight) * 1.86
        );
        const scaledHeight = baseHeight * scale;

        model.scale.set(scale);
        model.x = app.screen.width * 0.5;
        model.y = scaledHeight + app.screen.height * 0.02;
    }

    function canvasPointFromEvent(event) {
        const rect = canvas.getBoundingClientRect();
        const source = event.touches ? event.touches[0] : event;

        return {
            x: (source.clientX - rect.left) * (app.screen.width / rect.width),
            y: (source.clientY - rect.top) * (app.screen.height / rect.height),
        };
    }

    function focusModel(event) {
        if (!model || !app) {
            return;
        }

        const point = canvasPointFromEvent(event);
        model.focus(point.x, point.y);
    }

    function playIdleMotion() {
        if (!model) {
            return;
        }

        model.motion('Idle', undefined, MotionPriority.IDLE).catch(function () {
            // Some exported models only contain physics/eye-blink and no Idle motion group.
        });
    }

    async function loadVTubeSettings() {
        if (!vtubeSrc) {
            return null;
        }

        try {
            const response = await fetch(vtubeSrc);

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            return await response.json();
        } catch (error) {
            console.warn('Konfigurasi VTube/VBridger gagal dimuat:', error);
            return null;
        }
    }

    function setLive2DParam(paramId, value) {
        const coreModel = model && model.internalModel && model.internalModel.coreModel;

        if (!coreModel || !paramId) {
            return;
        }

        try {
            if (typeof coreModel.setParameterValueById === 'function') {
                coreModel.setParameterValueById(paramId, value);
                return;
            }

            if (typeof coreModel.setParamFloat === 'function') {
                coreModel.setParamFloat(paramId, value);
            }
        } catch (error) {
            // Ignore parameters that are present in VTube Studio settings but not in the web model.
        }
    }

    function mapInputToLive2D(inputName, inputValue) {
        const settings = vtubeSettings && Array.isArray(vtubeSettings.ParameterSettings)
            ? vtubeSettings.ParameterSettings
            : [];

        settings.forEach(function (setting) {
            if (setting.Input !== inputName || !setting.OutputLive2D) {
                return;
            }

            if (safeVBridgerParams.indexOf(setting.OutputLive2D) === -1) {
                return;
            }

            const inMin = Number(setting.InputRangeLower);
            const inMax = Number(setting.InputRangeUpper);
            const outMin = Number(setting.OutputRangeLower);
            const outMax = Number(setting.OutputRangeUpper);
            const safeInMax = inMax === inMin ? inMin + 1 : inMax;
            const normalized = Math.max(0, Math.min(1, (inputValue - inMin) / (safeInMax - inMin)));
            const mapped = outMin + normalized * (outMax - outMin);

            setLive2DParam(setting.OutputLive2D, mapped);
        });
    }

    function triggerVBridger(inputOverrides) {
        if (!model || !app || !vtubeSettings) {
            return;
        }

        if (vbridgerTicker) {
            app.ticker.remove(vbridgerTicker);
        }

        const inputs = Object.assign({
            MouthOpen: 0.55,
            JawOpen: 0.55,
            MouthSmile: 0.78,
            MouthFunnel: 0.28,
            MouthPucker: 0.35,
            CheekPuff: 0.45,
        }, inputOverrides || {});
        let elapsed = 0;

        vbridgerTicker = function (ticker) {
            elapsed += ticker.deltaMS;
            const progress = Math.min(elapsed / 720, 1);
            const strength = Math.sin(progress * Math.PI);

            Object.keys(inputs).forEach(function (inputName) {
                mapInputToLive2D(inputName, inputs[inputName] * strength);
            });

            if (progress >= 1) {
                app.ticker.remove(vbridgerTicker);
                vbridgerTicker = null;
            }
        };

        app.ticker.add(vbridgerTicker);
    }

    function bindEvents() {
        window.addEventListener('resize', fitModel);
        document.addEventListener('mousemove', focusModel, { passive: true });
    }

    async function bootLive2D() {
        try {
            createApp();

            model = await Live2DModel.from(modelSrc, {
                autoInteract: false,
                autoUpdate: true,
            });

            model.anchor.set(0.5, 1);
            app.stage.addChild(model);
            fitModel();
            bindEvents();
            playIdleMotion();
            idleTimer = window.setInterval(playIdleMotion, 18000);
            vtubeSettings = await loadVTubeSettings();
            window.triggerKaiVBridger = triggerVBridger;
            widget.classList.add('is-ready');
        } catch (error) {
            widget.classList.add('is-hidden');
            window.clearInterval(idleTimer);
            console.warn('Live2D gagal dimuat:', error);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootLive2D);
    } else {
        bootLive2D();
    }
}());
