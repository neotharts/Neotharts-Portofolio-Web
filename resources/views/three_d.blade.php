<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>3D Character - Neotharts</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite('resources/css/home.css')
    <style>
        :root {
            --orange: #FF9543;
            --black: #5A3F48;
        }

        body {
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            background: linear-gradient(135deg, #fef7f4 0%, #fff5f0 100%);
        }

        /* 3D Page Specific Styles */
        .three-d-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .three-d-header {
            text-align: center;
            padding: 100px 20px 40px;
        }

        .three-d-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 10px;
        }

        .three-d-header p {
            color: #8c7f74;
            font-size: 1rem;
        }

        .canvas-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
        }

        #three-d-canvas {
            width: 100%;
            max-width: 800px;
            height: 500px;
            border-radius: 24px;
            background: radial-gradient(circle at center, #ffeedd 0%, #fff5f0 50%, #fef7f4 100%);
            box-shadow: 0 20px 60px rgba(90, 63, 72, 0.15);
        }

        .controls-hint {
            text-align: center;
            padding: 20px;
            color: #8c7f74;
            font-size: 0.9rem;
        }

        .controls-hint span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 15px;
        }

        .controls-hint .material-icons {
            font-size: 18px;
        }

        /* Loading state */
        .canvas-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .canvas-loading .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f7f1ea;
            border-top: 3px solid var(--orange);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .three-d-header {
                padding: 80px 20px 30px;
            }

            .three-d-header h1 {
                font-size: 2rem;
            }

            #three-d-canvas {
                height: 400px;
            }

            .controls-hint {
                font-size: 0.8rem;
            }

            .controls-hint span {
                display: block;
                margin: 8px 0;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="mainav">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('artworks') }}">Artworks</a>
            <a href="{{ route('commission') }}">Commissions</a>
            <a href="{{ route('three_d') }}" class="active">3D</a>
            <a href="{{ route('contact') }}">Contact</a>
        </div>
        <div class="mainavmobile">
            <span class="material-icons">menu</span>
        </div>
    </nav>
    @include('partials.mobile-fullscreen-nav')

    <main class="three-d-page">
        <header class="three-d-header">
            <h1>3D Character</h1>
            <p>Interactive Calico Cat Character</p>
        </header>

        <div class="canvas-container">
            <div id="canvas-loading" class="canvas-loading">
                <div class="spinner"></div>
                <p>Loading 3D Character...</p>
            </div>
            <canvas id="three-d-canvas"></canvas>
        </div>

        <div class="controls-hint">
            <span><span class="material-icons">touch_app</span> Drag to rotate</span>
            <span><span class="material-icons">zoom_in</span> Scroll to zoom</span>
        </div>
    </main>

    <script type="importmap">
    {
        "imports": {
            "three": "https://unpkg.com/three@0.160.0/build/three.module.js"
        }
    }
    </script>
    <script type="module">
        import * as THREE from 'three';

        // Scene setup
        const canvas = document.getElementById('three-d-canvas');
        const scene = new THREE.Scene();

        const camera = new THREE.PerspectiveCamera(
            45,
            canvas.clientWidth / canvas.clientHeight,
            0.1,
            1000
        );
        camera.position.set(0, 1, 5);

        const renderer = new THREE.WebGLRenderer({
            canvas: canvas,
            antialias: true,
            alpha: true
        });
        renderer.setSize(canvas.clientWidth, canvas.clientHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;

        // Hide loading
        document.getElementById('canvas-loading').style.display = 'none';

        // Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        scene.add(ambientLight);

        const mainLight = new THREE.DirectionalLight(0xffffff, 1);
        mainLight.position.set(5, 10, 5);
        mainLight.castShadow = true;
        mainLight.shadow.mapSize.width = 1024;
        mainLight.shadow.mapSize.height = 1024;
        scene.add(mainLight);

        const fillLight = new THREE.DirectionalLight(0xffeedd, 0.4);
        fillLight.position.set(-5, 3, -5);
        scene.add(fillLight);

        // Calico Cat Character Group
        const catGroup = new THREE.Group();

        // Materials for calico pattern
        const orangeMaterial = new THREE.MeshStandardMaterial({
            color: 0xFF8C42,
            roughness: 0.8,
            metalness: 0.1
        });
        const whiteMaterial = new THREE.MeshStandardMaterial({
            color: 0xFFFAF5,
            roughness: 0.8,
            metalness: 0.1
        });
        const blackMaterial = new THREE.MeshStandardMaterial({
            color: 0x3D3D3D,
            roughness: 0.8,
            metalness: 0.1
        });
        const noseMaterial = new THREE.MeshStandardMaterial({
            color: 0xFFB6C1,
            roughness: 0.6,
            metalness: 0.1
        });
        const eyeMaterial = new THREE.MeshStandardMaterial({
            color: 0x2ECC71,
            roughness: 0.3,
            metalness: 0.2
        });
        const pupilMaterial = new THREE.MeshStandardMaterial({
            color: 0x1a1a1a,
            roughness: 0.2,
            metalness: 0.3
        });

        // Body (White base with patches)
        const bodyGeometry = new THREE.SphereGeometry(0.8, 32, 32);
        const body = new THREE.Mesh(bodyGeometry, whiteMaterial);
        body.scale.set(1, 0.8, 1.2);
        body.position.y = -0.2;
        body.castShadow = true;
        catGroup.add(body);

        // Body patches (orange and black spots)
        const orangePatch1 = new THREE.Mesh(
            new THREE.SphereGeometry(0.35, 16, 16),
            orangeMaterial
        );
        orangePatch1.position.set(0.3, 0, 0.4);
        orangePatch1.scale.set(1, 0.6, 0.8);
        catGroup.add(orangePatch1);

        const blackPatch1 = new THREE.Mesh(
            new THREE.SphereGeometry(0.3, 16, 16),
            blackMaterial
        );
        blackPatch1.position.set(-0.4, 0, -0.3);
        blackPatch1.scale.set(1, 0.5, 0.7);
        catGroup.add(blackPatch1);

        const orangePatch2 = new THREE.Mesh(
            new THREE.SphereGeometry(0.25, 16, 16),
            orangeMaterial
        );
        orangePatch2.position.set(-0.2, -0.3, 0.3);
        orangePatch2.scale.set(0.8, 0.5, 0.6);
        catGroup.add(orangePatch2);

        // Head
        const headGeometry = new THREE.SphereGeometry(0.5, 32, 32);
        const head = new THREE.Mesh(headGeometry, whiteMaterial);
        head.position.y = 0.6;
        head.castShadow = true;
        catGroup.add(head);

        // Head patches
        const headOrangePatch = new THREE.Mesh(
            new THREE.SphereGeometry(0.25, 16, 16),
            orangeMaterial
        );
        headOrangePatch.position.set(0.15, 0.7, 0.2);
        headOrangePatch.scale.set(1, 0.8, 0.6);
        catGroup.add(headOrangePatch);

        const headBlackPatch = new THREE.Mesh(
            new THREE.SphereGeometry(0.2, 16, 16),
            blackMaterial
        );
        headBlackPatch.position.set(-0.25, 0.55, 0.1);
        headBlackPatch.scale.set(1, 0.7, 0.5);
        catGroup.add(headBlackPatch);

        // Ears
        const earGeometry = new THREE.ConeGeometry(0.15, 0.3, 4);

        const leftEar = new THREE.Mesh(earGeometry, whiteMaterial);
        leftEar.position.set(-0.3, 1, 0);
        leftEar.rotation.z = 0.2;
        leftEar.castShadow = true;
        catGroup.add(leftEar);

        const leftEarInner = new THREE.Mesh(
            new THREE.ConeGeometry(0.08, 0.15, 4),
            noseMaterial
        );
        leftEarInner.position.set(-0.32, 0.98, 0.05);
        leftEarInner.rotation.z = 0.2;
        catGroup.add(leftEarInner);

        const rightEar = new THREE.Mesh(earGeometry, orangeMaterial);
        rightEar.position.set(0.3, 1, 0);
        rightEar.rotation.z = -0.2;
        rightEar.castShadow = true;
        catGroup.add(rightEar);

        const rightEarInner = new THREE.Mesh(
            new THREE.ConeGeometry(0.08, 0.15, 4),
            noseMaterial
        );
        rightEarInner.position.set(0.32, 0.98, 0.05);
        rightEarInner.rotation.z = -0.2;
        catGroup.add(rightEarInner);

        // Eyes
        const eyeGeometry = new THREE.SphereGeometry(0.1, 16, 16);

        const leftEye = new THREE.Mesh(eyeGeometry, eyeMaterial);
        leftEye.position.set(-0.2, 0.65, 0.4);
        leftEye.scale.set(0.8, 1, 0.5);
        catGroup.add(leftEye);

        const leftPupil = new THREE.Mesh(
            new THREE.SphereGeometry(0.05, 16, 16),
            pupilMaterial
        );
        leftPupil.position.set(-0.2, 0.65, 0.48);
        leftPupil.scale.set(0.8, 1, 0.5);
        catGroup.add(leftPupil);

        const rightEye = new THREE.Mesh(eyeGeometry, eyeMaterial);
        rightEye.position.set(0.2, 0.65, 0.4);
        rightEye.scale.set(0.8, 1, 0.5);
        catGroup.add(rightEye);

        const rightPupil = new THREE.Mesh(
            new THREE.SphereGeometry(0.05, 16, 16),
            pupilMaterial
        );
        rightPupil.position.set(0.2, 0.65, 0.48);
        rightPupil.scale.set(0.8, 1, 0.5);
        catGroup.add(rightPupil);

        // Nose
        const noseGeometry = new THREE.SphereGeometry(0.06, 16, 16);
        const nose = new THREE.Mesh(noseGeometry, noseMaterial);
        nose.position.set(0, 0.52, 0.48);
        nose.scale.set(1.2, 0.8, 0.5);
        catGroup.add(nose);

        // Mouth (simple line)
        const mouthCurve = new THREE.QuadraticBezierCurve3(
            new THREE.Vector3(-0.1, 0.42, 0.45),
            new THREE.Vector3(0, 0.38, 0.48),
            new THREE.Vector3(0.1, 0.42, 0.45)
        );
        const mouthGeometry = new THREE.TubeGeometry(mouthCurve, 8, 0.015, 8, false);
        const mouthMaterial = new THREE.MeshStandardMaterial({ color: 0x5A3F48 });
        const mouth = new THREE.Mesh(mouthGeometry, mouthMaterial);
        catGroup.add(mouth);

        // Whiskers
        const whiskerMaterial = new THREE.MeshStandardMaterial({
            color: 0x5A3F48,
            roughness: 0.5
        });

        // Left whiskers
        for (let i = 0; i < 3; i++) {
            const whiskerGeometry = new THREE.CylinderGeometry(0.003, 0.001, 0.3, 4);
            const whisker = new THREE.Mesh(whiskerGeometry, whiskerMaterial);
            whisker.position.set(-0.18 - i * 0.02, 0.48 - i * 0.03, 0.42);
            whisker.rotation.z = 0.3 + i * 0.15;
            whisker.rotation.y = 0.2;
            catGroup.add(whisker);
        }

        // Right whiskers
        for (let i = 0; i < 3; i++) {
            const whiskerGeometry = new THREE.CylinderGeometry(0.003, 0.001, 0.3, 4);
            const whisker = new THREE.Mesh(whiskerGeometry, whiskerMaterial);
            whisker.position.set(0.18 + i * 0.02, 0.48 - i * 0.03, 0.42);
            whisker.rotation.z = -0.3 - i * 0.15;
            whisker.rotation.y = -0.2;
            catGroup.add(whisker);
        }

        // Legs
        const legGeometry = new THREE.CylinderGeometry(0.12, 0.1, 0.4, 16);

        const frontLeftLeg = new THREE.Mesh(legGeometry, whiteMaterial);
        frontLeftLeg.position.set(-0.35, -0.5, 0.3);
        frontLeftLeg.castShadow = true;
        catGroup.add(frontLeftLeg);

        const frontRightLeg = new THREE.Mesh(legGeometry, orangeMaterial);
        frontRightLeg.position.set(0.35, -0.5, 0.3);
        frontRightLeg.castShadow = true;
        catGroup.add(frontRightLeg);

        const backLeftLeg = new THREE.Mesh(legGeometry, orangeMaterial);
        backLeftLeg.position.set(-0.35, -0.5, -0.3);
        backLeftLeg.castShadow = true;
        catGroup.add(backLeftLeg);

        const backRightLeg = new THREE.Mesh(legGeometry, whiteMaterial);
        backRightLeg.position.set(0.35, -0.5, -0.3);
        backRightLeg.castShadow = true;
        catGroup.add(backRightLeg);

        // Paws
        const pawGeometry = new THREE.SphereGeometry(0.12, 16, 16);

        const frontLeftPaw = new THREE.Mesh(pawGeometry, whiteMaterial);
        frontLeftPaw.position.set(-0.35, -0.75, 0.35);
        frontLeftPaw.scale.set(1, 0.5, 1.2);
        catGroup.add(frontLeftPaw);

        const frontRightPaw = new THREE.Mesh(pawGeometry, orangeMaterial);
        frontRightPaw.position.set(0.35, -0.75, 0.35);
        frontRightPaw.scale.set(1, 0.5, 1.2);
        catGroup.add(frontRightPaw);

        const backLeftPaw = new THREE.Mesh(pawGeometry, orangeMaterial);
        backLeftPaw.position.set(-0.35, -0.75, -0.35);
        backLeftPaw.scale.set(1, 0.5, 1.2);
        catGroup.add(backLeftPaw);

        const backRightPaw = new THREE.Mesh(pawGeometry, whiteMaterial);
        backRightPaw.position.set(0.35, -0.75, -0.35);
        backRightPaw.scale.set(1, 0.5, 1.2);
        catGroup.add(backRightPaw);

        // Tail
        const tailCurve = new THREE.CatmullRomCurve3([
            new THREE.Vector3(0, 0, -0.7),
            new THREE.Vector3(0, 0.2, -1),
            new THREE.Vector3(0.1, 0.4, -1.2),
            new THREE.Vector3(0.15, 0.5, -1.1)
        ]);
        const tailGeometry = new THREE.TubeGeometry(tailCurve, 20, 0.08, 8, false);
        const tail = new THREE.Mesh(tailGeometry, whiteMaterial);
        tail.castShadow = true;
        catGroup.add(tail);

        // Tail stripes (orange and black)
        const tailStripe1 = new THREE.Mesh(
            new THREE.TubeGeometry(
                new THREE.CatmullRomCurve3([
                    new THREE.Vector3(0, 0.15, -0.85),
                    new THREE.Vector3(0.05, 0.25, -1)
                ]),
                8, 0.09, 8, false
            ),
            orangeMaterial
        );
        catGroup.add(tailStripe1);

        const tailStripe2 = new THREE.Mesh(
            new THREE.TubeGeometry(
                new THREE.CatmullRomCurve3([
                    new THREE.Vector3(0.08, 0.35, -1.15),
                    new THREE.Vector3(0.12, 0.45, -1.1)
                ]),
                8, 0.09, 8, false
            ),
            blackMaterial
        );
        catGroup.add(tailStripe2);

        scene.add(catGroup);

        // Ground plane for shadow
        const groundGeometry = new THREE.CircleGeometry(3, 64);
        const groundMaterial = new THREE.ShadowMaterial({
            opacity: 0.2
        });
        const ground = new THREE.Mesh(groundGeometry, groundMaterial);
        ground.rotation.x = -Math.PI / 2;
        ground.position.y = -0.8;
        ground.receiveShadow = true;
        scene.add(ground);

        // Mouse controls
        let isDragging = false;
        let previousMousePosition = { x: 0, y: 0 };
        let targetRotationY = 0;
        let targetRotationX = 0;
        let currentRotationY = 0;
        let currentRotationX = 0;
        let zoomLevel = 5;

        canvas.addEventListener('mousedown', (e) => {
            isDragging = true;
            previousMousePosition = { x: e.clientX, y: e.clientY };
        });

        canvas.addEventListener('mousemove', (e) => {
            if (!isDragging) return;

            const deltaX = e.clientX - previousMousePosition.x;
            const deltaY = e.clientY - previousMousePosition.y;

            targetRotationY += deltaX * 0.01;
            targetRotationX += deltaY * 0.01;
            targetRotationX = Math.max(-0.5, Math.min(0.5, targetRotationX));

            previousMousePosition = { x: e.clientX, y: e.clientY };
        });

        canvas.addEventListener('mouseup', () => {
            isDragging = false;
        });

        canvas.addEventListener('mouseleave', () => {
            isDragging = false;
        });

        // Touch controls
        let touchStartY = 0;
        let initialPinchDistance = 0;

        canvas.addEventListener('touchstart', (e) => {
            if (e.touches.length === 1) {
                isDragging = true;
                previousMousePosition = { x: e.touches[0].clientX, y: e.touches[0].clientY };
            } else if (e.touches.length === 2) {
                initialPinchDistance = Math.hypot(
                    e.touches[0].clientX - e.touches[1].clientX,
                    e.touches[0].clientY - e.touches[1].clientY
                );
            }
        });

        canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();

            if (e.touches.length === 1 && isDragging) {
                const deltaX = e.touches[0].clientX - previousMousePosition.x;
                const deltaY = e.touches[0].clientY - previousMousePosition.y;

                targetRotationY += deltaX * 0.01;
                targetRotationX += deltaY * 0.01;
                targetRotationX = Math.max(-0.5, Math.min(0.5, targetRotationX));

                previousMousePosition = { x: e.touches[0].clientX, y: e.touches[0].clientY };
            } else if (e.touches.length === 2) {
                const currentDistance = Math.hypot(
                    e.touches[0].clientX - e.touches[1].clientX,
                    e.touches[0].clientY - e.touches[1].clientY
                );
                const delta = initialPinchDistance - currentDistance;
                zoomLevel += delta * 0.01;
                zoomLevel = Math.max(3, Math.min(8, zoomLevel));
                initialPinchDistance = currentDistance;
            }
        }, { passive: false });

        canvas.addEventListener('touchend', () => {
            isDragging = false;
        });

        // Scroll to zoom
        canvas.addEventListener('wheel', (e) => {
            e.preventDefault();
            zoomLevel += e.deltaY * 0.001;
            zoomLevel = Math.max(3, Math.min(8, zoomLevel));
        }, { passive: false });

        // Animation loop
        function animate() {
            requestAnimationFrame(animate);

            // Smooth rotation
            currentRotationY += (targetRotationY - currentRotationY) * 0.08;
            currentRotationX += (targetRotationX - currentRotationX) * 0.08;

            catGroup.rotation.y = currentRotationY;
            catGroup.rotation.x = currentRotationX;

            // Smooth zoom
            camera.position.z += (zoomLevel - camera.position.z) * 0.1;

            // Idle animation (subtle breathing)
            const time = Date.now() * 0.001;
            catGroup.position.y = Math.sin(time * 1.5) * 0.03;
            catGroup.scale.y = 1 + Math.sin(time * 1.5) * 0.01;

            // Subtle tail wag
            if (tail) {
                tail.rotation.z = Math.sin(time * 2) * 0.1;
            }

            renderer.render(scene, camera);
        }

        animate();

        // Handle resize
        window.addEventListener('resize', () => {
            const width = canvas.clientWidth;
            const height = canvas.clientHeight;

            camera.aspect = width / height;
            camera.updateProjectionMatrix();

            renderer.setSize(width, height);
        });
    </script>
    <script src="{{ asset('js/mobile-fullscreen-nav.js') }}"></script>
</body>
</html>
