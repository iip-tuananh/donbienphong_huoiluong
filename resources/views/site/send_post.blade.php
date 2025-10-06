@extends('site.layouts.master')
@section('title')Đóng góp bài viết - {{ $config->web_title }}@endsection
@section('description'){{ strip_tags(html_entity_decode($config->introduction)) }}@endsection
@section('image'){{@$config->image->path ?? ''}}@endsection

@section('css')
    <link type="text/css" rel="stylesheet" href="/site/css/editor-content.css">

@endsection

@section('content')
    <style>
        /* ====== Layout ====== */
        .submit-article { padding: 32px 16px; background: #fafafb; }
        .sa-container { max-width: 900px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 6px 24px rgba(0,0,0,.06); }
        .sa-title { text-align:center; font-size:28px; margin: 0 0 6px; font-weight:700; }
        .sa-intro { color:#4a5568; line-height:1.7; margin: 8px 0; }
        .sa-subtitle { margin-top: 20px; font-size: 20px; font-weight: 700; }

        .sa-alert { border-radius: 10px; padding: 12px 14px; margin: 14px 0; }
        .sa-alert--success{ background:#ecfdf5; color:#046c4e; border:1px solid #a7f3d0;}
        .sa-alert--error{ background:#fff1f2; color:#9f1239; border:1px solid #fecdd3;}
        .req{ color:#e11d48; }

        .sa-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-top: 10px; }
        .sa-field { display:flex; flex-direction:column; }
        .sa-field--full{ grid-column: 1 / -1; }
        .sa-field label { font-weight:600; margin-bottom:8px; }
        .sa-field input[type="text"],
        .sa-field input[type="email"],
        .sa-field textarea {
            border:1px solid #e5e7eb; border-radius:10px; padding: 12px 14px;
            outline: none; font-size: 15px; background:#fff;
        }
        .sa-field textarea{ resize: vertical; }
        .sa-field input:focus, .sa-field textarea:focus{ border-color:#27408B; box-shadow: 0 0 0 3px rgba(95,39,205,.12); }
        .sa-hint{ color:#6b7280; font-size:12px; margin-top:6px; }

        /* Dropzone */
        .sa-dropzone{
            position: relative; display:flex; align-items:center; justify-content:center; gap:10px;
            border:1.5px dashed #c7c8d2; border-radius:14px; padding: 18px; background:#f9fafb; cursor:pointer;
            transition: all .2s ease;
        }
        .sa-dropzone:focus{ outline:none; box-shadow:0 0 0 3px rgba(95,39,205,.15); border-color:#27408B; }
        .sa-dropzone:hover{ background:#f5f6ff; border-color:#27408B; }
        .sa-dropzone input[type="file"]{ position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; }
        .dz-icon{ font-size:22px; }
        .dz-text{ font-size:14px; color:#4b5563;}
        .dz-link{ color:#27408B; text-decoration:underline; cursor:pointer; }
        .sa-file-meta{ margin-top:8px; padding:10px 12px; background:#f3f4f6; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; color:#374151; }

        /* Actions */
        .sa-action{ display:flex; align-items:center; gap:16px; margin-top: 18px; }
        .sa-btn{
            position:relative; display:inline-flex; align-items:center; justify-content:center; gap:10px;
            background:#27408B; color:#fff; border:none; border-radius:12px; padding:12px 18px; font-weight:700;
            cursor:pointer; transition: transform .05s ease, box-shadow .2s ease; box-shadow: 0 8px 20px rgba(95,39,205,.25);
        }
        .sa-btn:hover{ transform: translateY(-1px); box-shadow:0 10px 22px rgba(95,39,205,.32); }
        .sa-btn:disabled{ opacity:.6; cursor:not-allowed; box-shadow:none; }
        .sa-btn__spinner{
            --s: 0; width:18px; height:18px; border-radius:50%; border:3px solid rgba(255,255,255,.35);
            border-top-color:#fff; animation: sa-spin .8s linear infinite; display:none;
        }
        .sa-btn.loading .sa-btn__spinner{ display:inline-block; }
        .sa-btn.loading .sa-btn__text{ opacity:.85; }
        @keyframes sa-spin { to { transform: rotate(360deg); } }

        .sa-legal{ color:#4b5563; font-size:14px; margin:0; }
        .sa-check{ display:flex; align-items:flex-start; gap:8px; }
        .sa-check input{ margin-top:2px; }

        /* Responsive */
        @media (max-width: 768px){
            .sa-grid{ grid-template-columns: 1fr; }
            .sa-container{ padding: 20px; }
            .sa-title{ font-size:24px; }
        }

        .invalid-feedback {
            color: #dc3545;
        }
        /* ClassicEditor */
        .ck-editor__editable_inline {
            min-height: 420px;   /* đổi số tùy ý */
            /* hoặc height: 420px; nếu muốn cố định */
        }
    </style>

    <style>
        /* Dành cho khung soạn thảo */
        .ck-content {
            font-weight: 400;                /* weight mặc định */
        }

        /* Bold/italic/underline phải hiện đúng */
        .ck-content strong, .ck-content b { font-weight: 700 !important; }
        .ck-content em, .ck-content i      { font-style: italic !important; }
        .ck-content u                      { text-decoration: underline !important; }

        /* Danh sách hiển thị chấm/số và có thụt lề */
        .ck-content ul { list-style: disc inside !important; }
        .ck-content ol { list-style: decimal inside !important;  }

        /* Blockquote cho dễ nhìn */
        .ck-content blockquote {
            border-left: 3px solid #e5e7eb;
            margin: .75rem 0; padding: .5rem .75rem;
            color: #555;
        }

    </style>
    <div class="content">
        <section>
            <div class="submit-article" ng-controller="Post" ng-cloak>
                <div class="sa-container">
                    <h1 class="sa-title">Đóng góp bài viết</h1>
                    <p class="sa-intro">
                        Tạp chí của chúng tôi hoan nghênh các bài viết, ý kiến và nội dung đóng góp từ cộng đồng.
                        Vui lòng đọc kỹ hướng dẫn biên tập trước khi gửi bài. Mọi bài gửi cần đảm bảo tính nguyên gốc
                        và tuân thủ quy định pháp luật về bản quyền.
                    </p>
                    <p class="sa-intro">
                        Để bài viết được xem xét, bạn hãy điền vào mẫu dưới đây (hoặc gửi qua email nếu cần).
                    </p>

                    {{-- Flash message --}}
                    @if (session('success'))
                        <div class="sa-alert sa-alert--success">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="sa-alert sa-alert--error">
                            <strong>Vui lòng kiểm tra lại:</strong>
                            <ul>
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h2 class="sa-subtitle">Gửi bài viết</h2>

                    <form id="articleForm" ng-submit="submit($event)"
                          enctype="multipart/form-data" novalidate>
                        <div class="sa-grid">
                            <div class="sa-field">
                                <label for="title">Tiêu đề bài viết <span class="req">*</span></label>
                                <input type="text" id="title" name="title" ng-model="post.title"  maxlength="180" placeholder="Nhập tiêu đề...">
                                <small class="sa-hint"><span id="titleCount">0</span>/180 ký tự</small>

                                <div class="invalid-feedback d-block" ng-if="errors['title']"><% errors['title'][0] %></div>
                            </div>

                            <div class="sa-field">
                                <label for="author_name">Tên tác giả <span class="req">*</span></label>
                                <input type="text" id="author_name" name="author_name" ng-model="post.author_name"  placeholder="Ví dụ: Nguyễn Văn A">

                                <div class="invalid-feedback d-block" ng-if="errors['author_name']"><% errors['author_name'][0] %></div>
                            </div>


{{--                            <div class="sa-field sa-field--full">--}}
{{--                                <label for="contentEditor">Nội dung bài viết</label>--}}
{{--                                <textarea id="contentEditor" name="content_html"></textarea>--}}
{{--                                <small class="sa-hint">Trình soạn thảo hỗ trợ định dạng, ảnh, tiêu đề...</small>--}}
{{--                            </div>--}}


                            <div class="sa-field sa-field--full">
                                <label>Nội dung bài viết</label>

                                <div id="contentEditor" class="ck-content" style="min-height:280px;"></div>


                                <textarea id="content_html" name="content_html" hidden></textarea>

                                <small class="sa-hint">Trình soạn thảo hỗ trợ định dạng cơ bản.</small>
                            </div>

                            <div class="sa-field sa-field--full">
                                <label>Tệp đính kèm (Doc/Docx/PDF, tối đa 10MB)</label>

                                <div id="dropzone" class="sa-dropzone" tabindex="0">
                                    <div class="dz-icon">📄</div>
                                    <div class="dz-text">
                                        Kéo & thả tệp vào đây hoặc <span class="dz-link">chọn tệp</span>
                                    </div>
                                    <input id="fileInput" type="file" name="attachment" file-model="post.attachment"
                                           accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf">
                                </div>
                                <div id="fileMeta" class="sa-file-meta" style="display:none;"></div>
                                <small class="sa-hint">Chỉ chấp nhận .doc, .docx, .pdf</small>
                            </div>
                        </div>

                        <div class="sa-action">
                            <button id="submitBtn" type="submit" class="sa-btn" ng-class="{'loading': loading}" ng-disabled="loading">
                                <span class="sa-btn__spinner" aria-hidden="true"></span>
                                <span class="sa-btn__text">Gửi</span>
                            </button>
                            <p class="sa-legal">
                                <label class="sa-check">
                                    <input type="checkbox" name="copyright_confirm" value="1" required>
                                    Tôi xác nhận nội dung là của tôi hoặc được phép sử dụng, và đồng ý cho tòa soạn biên tập/xuất bản.
                                </label>
                            </p>


                        </div>
                        <div id="submitSuccess" class="sa-alert sa-alert--success" ng-if="submitNotice.show"  role="status" aria-live="polite">
                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                            <div class="sa-alert__text">
                                <strong>Cảm ơn bạn đã đóng góp bài viết.</strong>
                                Bạn có thể xem các bài viết đã gửi trong menu <em>Quản lý tài khoản</em>.
                            </div>
                            <button type="button" class="sa-alert__close" ng-click="submitNotice.show=false" aria-label="Đóng">&times;</button>
                        </div>
                    </form>
                </div>
            </div>

            <style>
                .sa-alert{
                    display:flex; align-items:flex-start; gap:10px;
                    padding:12px 14px; border-radius:10px; margin:12px 0;
                    border:1px solid transparent; box-shadow:0 6px 18px rgba(10,37,64,.06);
                }
                .sa-alert i{ font-size:20px; line-height:1; margin-top:2px; }
                .sa-alert__text{ line-height:1.5; color:#1f2937; }
                .sa-alert__close{
                    margin-left:auto; border:none; background:transparent; cursor:pointer;
                    font-size:20px; line-height:1; color:#6b7280;
                }
                .sa-alert__close:hover{ color:#111827; }
                .sa-alert--success{
                    background:#ecfdf5; border-color:#a7f3d0; /* xanh lá nhạt */
                }
                .sa-alert--success i{ color:#059669; }

                @media (max-width: 480px){
                    .sa-alert{ padding:10px 12px; border-radius:12px; }
                    .sa-alert i{ font-size:18px; }
                }
            </style>


        </section>
    </div>
    @if (!auth('customer')->check())
        <div id="loginRequireModal" class="lr-backdrop" role="dialog" aria-modal="true" aria-labelledby="lrTitle" aria-describedby="lrDesc">
            <div class="lr-card">
                <div class="lr-icon" aria-hidden="true">🔒</div>
                <h3 id="lrTitle" class="lr-title">Cần đăng nhập để tiếp tục</h3>
                <p id="lrDesc" class="lr-desc">
                    Vui lòng đăng nhập trước khi gửi bài viết. Bạn có thể quản lý các bài đã gửi trong mục <strong>Quản lý tài khoản</strong>.
                </p>
                <div class="lr-actions">
                    <a class="lr-btn lr-primary" href="{{ route('front.home-page') }}#login">Đăng nhập</a>
                </div>
            </div>
        </div>

        <style>
            /* Khóa cuộn & làm mờ phần nội dung khi modal mở */
            body.lr-open { overflow: hidden; }
            body.lr-open .content { filter: blur(4px); pointer-events: none; }

            /* Backdrop phủ toàn màn hình */
            .lr-backdrop{
                position: fixed; inset: 0;
                background: rgba(17,24,39,.55);
                backdrop-filter: blur(2px);
                display: grid; place-items: center;
                z-index: 2147483000;
                padding: 16px;
                animation: lrFade .15s ease-out both;
            }
            @keyframes lrFade { from { opacity: 0 } to { opacity: 1 } }

            /* Thẻ thông báo */
            .lr-card{
                width: min(520px, 92vw);
                background: #fff;
                border-radius: 14px;
                padding: 20px 18px;
                box-shadow: 0 18px 60px rgba(0,0,0,.25);
                text-align: center;
                animation: lrPop .18s ease-out both;
            }
            @keyframes lrPop {
                from { transform: translateY(6px) scale(.98); opacity:.9 }
                to   { transform: translateY(0)   scale(1);    opacity:1 }
            }

            .lr-icon{ font-size: 36px; margin-bottom: 8px }
            .lr-title{ margin: 0 0 6px; font-size: 20px; font-weight: 800; color:#111827 }
            .lr-desc{ margin: 0 8px 16px; color:#4b5563; line-height: 1.55 }

            .lr-actions{ display: flex; gap: 10px; justify-content: center; }
            .lr-btn{
                display:inline-flex; align-items:center; justify-content:center;
                padding: 10px 14px; border-radius: 999px; text-decoration:none; font-weight:700;
                border: 1px solid transparent; min-width: 120px;
            }
            .lr-primary{ background:#27408B; color:#fff; box-shadow:0 8px 22px rgba(95,39,205,.22); }
            .lr-primary:hover{ filter: brightness(0.96); }
            .lr-ghost{ background:#fff; color:#111827; border-color:#E5E7EB; }
            .lr-ghost:hover{ background:#F9FAFB; }

            @media (max-width: 480px){
                .lr-card{ padding:16px 14px; border-radius:12px }
                .lr-title{ font-size:18px }
                .lr-desc{ font-size:14px }
                .lr-actions{ flex-direction: column; }
                .lr-btn{ width:100% }
            }
        </style>

        <script>
            // Mở modal ngay khi DOM sẵn sàng
            document.addEventListener('DOMContentLoaded', function(){
                document.body.classList.add('lr-open');
                // Không cho đóng bằng ESC hoặc click nền để "buộc" đăng nhập
                // Nếu muốn cho đóng tạm thời, bạn có thể gỡ bỏ đoạn chặn dưới.
                window.addEventListener('keydown', function(e){
                    if (e.key === 'Escape') e.preventDefault();
                });
                document.getElementById('loginRequireModal').addEventListener('click', function(e){
                    // chặn click nền
                    if (e.target === this) e.preventDefault();
                });
            });
        </script>
    @endif
@endsection

@push('scripts')

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

    <script>
        (function(){
            // Char counter for title
            const title = document.getElementById('title');
            const titleCount = document.getElementById('titleCount');
            const updateCount = () => titleCount.textContent = (title.value || '').length;
            title.addEventListener('input', updateCount); updateCount();

            let editor;

            ClassicEditor.create(document.querySelector('#contentEditor'), {
                toolbar: ['heading','|','bold','italic','link','|','bulletedList','numberedList','|','blockQuote','|','undo','redo']
            }).then(e => {
                editor = e;
                // nếu form edit có sẵn dữ liệu, đổ vào editor
                const hidden = document.getElementById('content_html');
                if (hidden.value) editor.setData(hidden.value);

                // luôn đồng bộ về textarea mỗi khi gõ
                editor.model.document.on('change:data', () => {
                    hidden.value = editor.getData();
                });

                // tiện: expose để nơi khác có thể gọi
                window._ck = editor;
            }).catch(console.error);





            // Dropzone + file validation
            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('fileInput');
            const fileMeta  = document.getElementById('fileMeta');
            const MAX_SIZE = 10 * 1024 * 1024; // 10MB
            const ALLOW = ['application/pdf','application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

            function renderFileMeta(file){
                fileMeta.style.display = 'block';
                fileMeta.textContent = `${file.name} — ${(file.size/1024/1024).toFixed(2)} MB`;
            }
            function validateFile(file){
                if (!file) return true;
                if (!ALLOW.includes(file.type) && !/\.(pdf|doc|docx)$/i.test(file.name)) {
                    alert('Tệp không hợp lệ. Chỉ chấp nhận .doc, .docx hoặc .pdf');
                    fileInput.value = '';
                    fileMeta.style.display = 'none';
                    return false;
                }
                if (file.size > MAX_SIZE){
                    alert('Kích thước tệp vượt quá 10MB');
                    fileInput.value = '';
                    fileMeta.style.display = 'none';
                    return false;
                }
                return true;
            }
            fileInput.addEventListener('change', (e)=>{
                const f = e.target.files[0];
                if (validateFile(f)) renderFileMeta(f);
            });

            // Drag & drop UX
            ['dragenter','dragover'].forEach(evt => {
                dropzone.addEventListener(evt, e => {
                    e.preventDefault(); e.stopPropagation();
                    dropzone.style.borderColor = '#27408B';
                    dropzone.style.background = '#eef0ff';
                });
            });
            ['dragleave','drop'].forEach(evt => {
                dropzone.addEventListener(evt, e => {
                    e.preventDefault(); e.stopPropagation();
                    dropzone.style.borderColor = '#c7c8d2';
                    dropzone.style.background = '#f9fafb';
                });
            });
            dropzone.addEventListener('drop', e => {
                const f = e.dataTransfer.files[0];
                if (validateFile(f)) {
                    // put file into input
                    const dt = new DataTransfer();
                    dt.items.add(f);
                    fileInput.files = dt.files;
                    renderFileMeta(f);
                }
            });

            // Submit handler: show loading, simple client-side check
        })();
    </script>

    <script>
        app.controller('Post', function ($rootScope, $scope, $sce, $interval) {
            $scope.errors = [];
            $scope.post = {};
            $scope.loading = false;

            function resetSendPostForm($scope){
                const form = document.getElementById('articleForm');
                if (form) form.reset();

                // CKEditor 5
                if (window._ck && typeof window._ck.setData === 'function') {
                    window._ck.setData('');
                }

                // File input + dropzone meta
                const fileInput = document.getElementById('fileInput');
                if (fileInput) fileInput.value = '';
                const fileMeta = document.getElementById('fileMeta');
                if (fileMeta){ fileMeta.style.display = 'none'; fileMeta.textContent = ''; }
                const dropzone = document.getElementById('dropzone');
                if (dropzone){
                    dropzone.style.borderColor = '#c7c8d2';
                    dropzone.style.background  = '#f9fafb';
                }

                // Đếm ký tự tiêu đề
                const title = document.getElementById('title');
                const titleCount = document.getElementById('titleCount');
                if (title && titleCount){
                    titleCount.textContent = (title.value || '').length;
                } else if (titleCount){
                    titleCount.textContent = '0';
                }

                // Angular models
                if ($scope){
                    $scope.post   = {};
                    $scope.errors = {};
                    $scope.loading = false;
                }

                // Nút gửi (nếu có class loading)
                const submitBtn = document.getElementById('submitBtn');
                if (submitBtn){
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                }
            }

            $scope.submitNotice = { show: false };

            $scope.submit = function (e) {
                if (e) e.preventDefault();
                var form = document.getElementById('articleForm');
                if (!form.checkValidity()) { form.reportValidity(); return; }

                var contentHtml = (window._ck ? window._ck.getData() : document.getElementById('contentEditor').value || '');
                var fd = new FormData();
                fd.append('title', $scope.post.title || '');
                fd.append('author_name', $scope.post.author_name || '');
                fd.append('author_email', $scope.post.author_email || '');
                fd.append('content_html', contentHtml);
                if ($scope.post.attachment) fd.append('attachment', $scope.post.attachment);
                $scope.loading = true;

                $.ajax({
                    type: 'POST',
                    url:  "{{ route('front.submitSendPost') }}",
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            $scope.errors = [];
                            toastr.success(response.message);
                            $scope.submitNotice.show = true;

                            if (typeof $scope.$evalAsync === 'function') {
                                $scope.$evalAsync(function(){
                                    resetSendPostForm($scope);

                                    // cuộn tới thông báo
                                    var el = document.getElementById('submitSuccess');
                                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });

                                });
                            } else {
                                resetSendPostForm($scope);
                                if (typeof $scope.$apply === 'function') $scope.$apply();

                                // cuộn tới thông báo
                                var el2 = document.getElementById('submitSuccess');
                                if (el2) el2.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        } else {
                            $scope.errors = response.errors;
                            toastr.warning(response.message);
                        }
                    },
                    error: function () {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function () {
                        $scope.loading = false;
                        $scope.$apply();
                    }
                });


            }
        })
    </script>

@endpush
