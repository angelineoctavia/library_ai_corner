<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Corner - Control Panel</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: url("{{ asset('images/Mac_Background.jpg') }}") no-repeat center center fixed;
            background-size: cover;
            font-family: 'Inter', sans-serif;
            color: #ffffff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            position: relative;
        }

        .panel-container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            margin: 80px auto 0 auto;
            padding: 0 20px;
            justify-content: space-between;
            align-items: stretch;
            z-index: 10;
            height: calc(100vh - 194px);
        }

        .glass-box-right {
            width: 580px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(151, 252, 255, 0.4);
            border-radius: 24px;
            backdrop-filter: blur(18px);
            padding: 35px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45), 0 0 25px rgba(151, 252, 255, 0.15);
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            height: 100%;
            /* Mengikuti tinggi flex container kiri */
        }

        /* Buat area scrollable fleksibel mengisi sisa tinggi box */
        .table-scroll {
            flex: 1;
            min-height: 0;
            max-height: none;
            /* Hapus batasan pixel fix sebelumnya */
            overflow-y: auto;
            border-radius: 12px;
        }

        .section-title {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #ffca7a;
            text-shadow:
                0 0 8px rgba(255, 202, 122, 0.9),
                0 0 18px rgba(255, 177, 66, 0.8);
        }

        .glass-box {
            width: 540px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(151, 252, 255, 0.4);
            border-radius: 24px;
            backdrop-filter: blur(18px);
            padding: 35px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45), 0 0 25px rgba(151, 252, 255, 0.15);
            display: flex;
            flex-direction: column;
        }

        .form-group {
            width: 100%;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            color: #ffca7a;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .input-wrapper input {
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 12px;
            padding: 14px 18px;
            color: #2c2c2c;
            font-size: 15px;
            outline: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .logo-upload-box {
            width: 100%;
            min-height: 220px;
            background: rgba(255, 255, 255, 0.95);
            border: 2px dashed rgba(255, 159, 67, 0.6);
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 25px 20px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .logo-upload-box:hover,
        .logo-upload-box.dragover {
            background: rgba(255, 255, 255, 1);
            border-color: #ff9f43;
            box-shadow: 0 0 20px rgba(255, 159, 67, 0.3);
        }

        .upload-icon-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 5px;
        }

        .upload-folder-icon {
            width: 56px;
            height: 56px;
            stroke: #ff9f43;
            transition: transform 0.2s;
        }

        .logo-upload-box:hover .upload-folder-icon {
            transform: scale(1.05);
        }

        .divider-or {
            display: flex;
            align-items: center;
            width: 70%;
            margin: 12px 0;
            text-align: center;
        }

        .divider-or::before,
        .divider-or::after {
            content: '';
            flex: 1;
            border-bottom: 1.5px solid rgba(255, 159, 67, 0.4);
        }

        .divider-or span {
            padding: 0 12px;
            color: #ff9f43;
            font-size: 14px;
            font-weight: bold;
        }

        .upload-text-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            max-width: 90%;
        }

        .upload-text {
            color: #ff9f43;
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .remove-file-btn {
            background: rgba(255, 71, 87, 0.15);
            color: #ff4757;
            border: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            font-size: 14px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .remove-file-btn:hover {
            background: #ff4757;
            color: white;
            transform: scale(1.1);
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #ff9f43 0%, #ff7675 100%);
            border: none;
            border-radius: 12px;
            padding: 15px;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(255, 159, 67, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 5px;
        }

        .submit-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(255, 159, 67, 0.6);
        }

        .status-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }

        .status-tab {
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
            opacity: 0.5;
            transition: opacity 0.2s, transform 0.15s;
            color: #fff;
        }

        .status-tab:hover {
            transform: translateY(-1px);
        }

        .status-tab.active-tab-btn {
            opacity: 1;
        }

        .status-tab.tab-active {
            background: #2ecc71;
        }

        .status-tab.tab-inactive {
            background: #ff6b6b;
        }

        .btn-restore {
            background: #2ecc71;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        .tools-table-panel {
            display: none;
            flex-direction: column;
            flex: 1;
            overflow: hidden;
        }

        .tools-table-panel.tools-table-visible {
            display: flex;
        }

        .table-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .table-scroll::-webkit-scrollbar-thumb {
            background: rgba(242, 125, 0, 0.5);
            border-radius: 10px;
        }

        .table-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .table-container {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(151, 252, 255, 0.2);
        }

        .table-container thead th {
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .table-container th {
            background: #f27d00;
            color: #ffffff;
            font-weight: bold;
            padding: 14px 16px;
            text-align: left;
            font-size: 14px;
        }

        .table-container td {
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.85);
            color: #2c2c2c;
            font-size: 14px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        .table-container tr:last-child td {
            border-bottom: none;
        }

        .action-btns {
            display: flex;
            gap: 8px;
        }

        .btn-edit {
            background: #007aff;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-delete {
            background: #ff4757;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>

<body>
    @include('admin.partials.navbar')

    <div class="panel-container">

        <!-- Sisi Kiri: Upload Form -->
        <div>
            <div class="section-title" id="form-section-title">Upload</div>
            <div class="glass-box">
                <form action="{{ route('admin.ai.store') }}" method="POST" enctype="multipart/form-data" id="ai-form">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    <input type="hidden" name="ai_id" id="ai-id-input">

                    <div class="form-group">
                        <label>AI Name</label>
                        <div class="input-wrapper">
                            <input type="text" name="ai_name" id="ai-name-input" placeholder="Enter AI name"
                                autocomplete="off">
                        </div>
                        <small class="error-message" id="error-name"
                            style="color: #ff4757; font-size: 12px; display: none; margin-top: 4px;">Field is
                            required</small>
                    </div>

                    <div class="form-group">
                        <label>AI URL Link</label>
                        <div class="input-wrapper">
                            <input type="text" name="ai_url" id="ai-url-input" placeholder="Enter URL link"
                                autocomplete="off">
                        </div>
                        <small class="error-message" id="error-url"
                            style="color: #ff4757; font-size: 12px; display: none; margin-top: 4px;">Field is
                            required</small>
                    </div>

                    <div class="form-group">
                        <label>Logo</label>
                        <div class="logo-upload-box" id="drop-zone">
                            <div class="upload-icon-container" onclick="document.getElementById('file-input').click();">
                                <svg class="upload-folder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z">
                                    </path>
                                    <line x1="12" y1="11" x2="12" y2="17"></line>
                                    <polyline points="9 14 12 11 15 14"></polyline>
                                </svg>
                            </div>

                            <div class="divider-or">
                                <span>or</span>
                            </div>

                            <div class="upload-text-container">
                                <span class="upload-text" id="file-name">drag and drop here</span>
                                <button type="button" id="remove-file-btn" class="remove-file-btn"
                                    style="display: none;" title="Hapus file">&times;</button>
                            </div>

                            <input type="file" name="ai_icon" id="file-input" style="display: none;">
                        </div>
                        <small class="error-message" id="error-file"
                            style="color: #ff4757; font-size: 12px; display: none; margin-top: 4px;">Field is
                            required (Opsional saat Edit jika tidak ingin mengubah logo)</small>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="button" id="cancel-edit-btn" class="submit-btn"
                            style="background: #6c757d; display: none;">Cancel</button>
                        <button type="submit" class="submit-btn" id="submit-btn-text">Save AI Tool</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sisi Kanan: List Table -->
        <div>
            <div class="section-title">List</div>
            <div class="glass-box-right">

                <div class="status-tabs">
                    <button type="button" class="status-tab tab-active active-tab-btn"
                        data-target="panel-active">Active</button>
                    <button type="button" class="status-tab tab-inactive"
                        data-target="panel-inactive">Inactive</button>
                </div>

                <!-- Panel Active -->
                <div class="tools-table-panel tools-table-visible" id="panel-active">
                    <div class="table-scroll">
                        <table class="table-container">
                            <thead>
                                <tr>
                                    <th>AI Name</th>
                                    <th>URL Link</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($aiTools as $tool)
                                    <tr>
                                        <td>{{ $tool->ai_name }}</td>
                                        <td>{{ $tool->ai_url }}</td>
                                        <td>
                                            <div class="action-btns">
                                                <button type="button" class="btn-edit"
                                                    data-id="{{ $tool->ai_id ?? $tool->id }}"
                                                    data-name="{{ $tool->ai_name }}" data-url="{{ $tool->ai_url }}">
                                                    Edit
                                                </button>

                                                <form
                                                    action="{{ route('admin.ai.delete', $tool->ai_id ?? $tool->id) }}"
                                                    method="POST" class="delete-form" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn-delete btn-trigger-delete">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="text-align:center; color:#777;">Belum ada AI tool
                                            aktif.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Panel Inactive -->
                <div class="tools-table-panel" id="panel-inactive">
                    <div class="table-scroll">
                        <table class="table-container">
                            <thead>
                                <tr>
                                    <th>AI Name</th>
                                    <th>URL Link</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($inactiveTools as $tool)
                                    <tr>
                                        <td>{{ $tool->ai_name }}</td>
                                        <td>{{ $tool->ai_url }}</td>
                                        <td>
                                            <div class="action-btns">
                                                <form
                                                    action="{{ route('admin.ai.restore', $tool->ai_id ?? $tool->id) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn-restore">Restore</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="text-align:center; color:#777;">Belum ada AI tool
                                            nonaktif.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- Custom Delete Modal Confirmation -->
        <div id="delete-modal"
            style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center;">
            <div
                style="background: #ffffff; padding: 25px 30px; border-radius: 16px; width: 350px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2); animation: fadeIn 0.3s ease;">
                <h3 style="margin-bottom: 10px; color: #2c2c2c; font-size: 18px;">Konfirmasi Hapus</h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Yakin ingin menghapus AI
                    ini?</p>
                <div style="display: flex; justify-content: center; gap: 10px;">
                    <button type="button" id="cancel-delete-btn"
                        style="padding: 8px 16px; border: 1px solid #ddd; background: #f8f9fa; color: #333; border-radius: 8px; cursor: pointer; font-weight: 500;">Batal</button>
                    <button type="button" id="confirm-delete-btn"
                        style="padding: 8px 16px; border: none; background: #ff4757; color: white; border-radius: 8px; cursor: pointer; font-weight: 500;">Ya,
                        Hapus</button>
                </div>
            </div>
        </div>

    </div>
    <div class="bottom-bar"></div>
</body>

<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const fileNameDisplay = document.getElementById('file-name');
    const removeFileBtn = document.getElementById('remove-file-btn');
    const aiForm = document.getElementById('ai-form');

    const aiNameInput = document.getElementById('ai-name-input');
    const aiUrlInput = document.getElementById('ai-url-input');
    const aiIdInput = document.getElementById('ai-id-input');
    const formMethod = document.getElementById('form-method');
    const formSectionTitle = document.getElementById('form-section-title');
    const submitBtnText = document.getElementById('submit-btn-text');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');

    const errorName = document.getElementById('error-name');
    const errorUrl = document.getElementById('error-url');
    const errorFile = document.getElementById('error-file');

    let isEditMode = false;
    let isSubmitting = false;

    function handleFile(file) {
        if (file) {
            fileNameDisplay.textContent = file.name;
            fileNameDisplay.style.color = '#2c2c2c';
            removeFileBtn.style.display = 'flex';
            errorFile.style.display = 'none';
            dropZone.style.borderColor = 'rgba(255, 159, 67, 0.6)';
        }
    }

    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function() {
            isEditMode = true;
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const url = this.getAttribute('data-url');

            aiIdInput.value = id;
            aiNameInput.value = name;
            aiUrlInput.value = url;

            aiForm.action = `/admin/ai/update/${id}`;
            formMethod.value = 'PUT';

            formSectionTitle.textContent = 'Edit AI Tool';
            submitBtnText.textContent = 'Update Changes';
            cancelEditBtn.style.display = 'block';

            errorName.style.display = 'none';
            errorUrl.style.display = 'none';
            aiNameInput.style.borderColor = '';
            aiUrlInput.style.borderColor = '';

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });

    cancelEditBtn.addEventListener('click', function() {
        resetFormToDefault();
    });

    function resetFormToDefault() {
        isEditMode = false;
        aiForm.action = "{{ route('admin.ai.store') }}";
        formMethod.value = 'POST';
        aiIdInput.value = '';
        aiNameInput.value = '';
        aiUrlInput.value = '';

        formSectionTitle.textContent = 'Upload';
        submitBtnText.textContent = 'Save AI Tool';
        cancelEditBtn.style.display = 'none';

        fileInput.value = '';
        fileNameDisplay.textContent = 'drag and drop here';
        removeFileBtn.style.display = 'none';
    }

    // Form Submit Event (Dilengkapi pencegah double-click)
    aiForm.addEventListener('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return;
        }

        let isValid = true;

        if (!aiNameInput.value.trim()) {
            errorName.style.display = 'block';
            aiNameInput.style.borderColor = '#ff4757';
            isValid = false;
        } else {
            errorName.style.display = 'none';
            aiNameInput.style.borderColor = '';
        }

        if (!aiUrlInput.value.trim()) {
            errorUrl.style.display = 'block';
            aiUrlInput.style.borderColor = '#ff4757';
            isValid = false;
        } else {
            errorUrl.style.display = 'none';
            aiUrlInput.style.borderColor = '';
        }

        if (!isEditMode && fileInput.files.length === 0) {
            errorFile.style.display = 'block';
            dropZone.style.borderColor = '#ff4757';
            isValid = false;
        } else {
            errorFile.style.display = 'none';
            dropZone.style.borderColor = 'rgba(255, 159, 67, 0.6)';
        }

        if (!isValid) {
            e.preventDefault();
        } else {
            isSubmitting = true;
            submitBtnText.textContent = 'Saving...';
        }
    });

    aiNameInput.addEventListener('input', () => {
        if (aiNameInput.value.trim()) {
            errorName.style.display = 'none';
            aiNameInput.style.borderColor = '';
        }
    });

    aiUrlInput.addEventListener('input', () => {
        if (aiUrlInput.value.trim()) {
            errorUrl.style.display = 'none';
            aiUrlInput.style.borderColor = '';
        }
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('dragover');
        }, false);
    });

    dropZone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFile(files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFile(fileInput.files[0]);
        }
    });

    removeFileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        fileInput.value = '';
        fileNameDisplay.textContent = 'drag and drop here';
        fileNameDisplay.style.color = '#ff9f43';
        removeFileBtn.style.display = 'none';
    });

    const deleteModal = document.getElementById('delete-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    let activeFormToSubmit = null;

    document.querySelectorAll('.btn-trigger-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            activeFormToSubmit = this.closest('form');
            deleteModal.style.display = 'flex';
        });
    });

    cancelDeleteBtn.addEventListener('click', function() {
        deleteModal.style.display = 'none';
        activeFormToSubmit = null;
    });

    confirmDeleteBtn.addEventListener('click', function() {
        if (activeFormToSubmit) {
            activeFormToSubmit.submit();
        }
    });

    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            deleteModal.style.display = 'none';
            activeFormToSubmit = null;
        }
    });

    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.status-tab').forEach(t => t.classList.remove('active-tab-btn'));
            this.classList.add('active-tab-btn');

            document.querySelectorAll('.tools-table-panel').forEach(p => p.classList.remove(
                'tools-table-visible'));
            document.getElementById(this.dataset.target).classList.add('tools-table-visible');
        });
    });
</script>
</html>
