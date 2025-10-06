@extends('site.layouts.master')
@section('title')
    {{ $config->web_title }}
@endsection
@section('description')
    {{ strip_tags(html_entity_decode($config->introduction)) }}
@endsection
@section('image')
    {{@$config->image->path ?? ''}}
@endsection

@section('css')

@endsection

@section('content')

    <div class="content" ng-controller="profilePage" style="margin-top: 90px">
        <!--section   -->
        <div class="breadcrumbs-header fl-wrap">
            <div class="container">
                <div class="breadcrumbs-header_url">
                    <a href="{{ route('front.home-page') }}">Trang chủ</a><span>Thông tin tài khoản</span>
                </div>

            </div>
            <div class="pwh_bg"></div>
        </div>
        <!-- section end  -->
        <!--section   -->
        <section class="account-page">
            <div class="container">
                <div class="account-grid">
                    <!-- SIDEBAR -->
                    <aside class="account-aside">
                        <div class="user-card">
                            <div class="avatar-user">
                                <img
                                    ng-src="<% avatarPreviewUrl %>"
                                    alt="Avatar">
                                <input id="avatar-input" type="file" accept="image/*" file-model="form.avatar" style="display:none">

                                <button class="avatar-edit" type="button" title="Đổi ảnh đại diện" ng-click="pickAvatar()">
                                    <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 8a4 4 0 100 8 4 4 0 000-8zm7-3h-2.2l-.7-1.4A2 2 0 0014.3 2H9.7a2 2 0 00-1.8 1.1L7.2 5H5a2 2 0 00-2 2v10a3 3 0 003 3h12a3 3 0 003-3V7a2 2 0 00-2-2z" fill="currentColor"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="user-info">
                                <strong class="name">{{ $customer->fullname }}</strong>
                                <span class="email">{{ $customer->email }}</span>
                                <a class="btn btn-ghost btn-logout">Đăng xuất</a>
                            </div>
                        </div>

                        <nav class="account-menu" aria-label="Menu tài khoản">
                            <a class="menu-item  is-active" href="#info" data-tab="info">
                                <span class="mi-icon">✎</span> Thay đổi thông tin tài khoản
                            </a>
                            <a class="menu-item " href="#orders" data-tab="orders">
                                <span class="mi-icon">🧾</span> Bài viết đóng góp
                            </a>
                            <a class="menu-item btn-logout" href="#">
                                <span class="mi-icon">↩</span> Đăng xuất
                            </a>
                        </nav>
                    </aside>

                    <!-- MAIN -->
                    <main class="account-main">
                        <!-- Tab: Thông tin -->
                        <div class="panel  is-active" id="info" role="tabpanel" aria-hidden="true">
                            <h3 class="panel-title">Thông tin tài khoản</h3>

                            <form action="" method="post" class="form-styled">
                                @csrf
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="display_name">Họ tên <span class="req">*</span></label>
                                        <input id="display_name" name="display_name" type="text"
                                               ng-model="form.fullname">
                                        <small class="hint"></small>
                                        <div class="invalid-feedback d-block" ng-if="errors['fullname']"><% errors['fullname'][0] %></div>

                                    </div>
                                    <div class="form-group">
                                        <label for="email">Địa chỉ email <span class="req">*</span></label>
                                        <input id="email" name="email" type="email"
                                               ng-model="form.email" disabled>
                                    </div>
                                </div>

                                <h4 class="panel-subtitle">Thay đổi mật khẩu</h4>
                                <div class="form-grid">
                                    <div class="form-group col-span-2">
                                        <label for="current_password">Mật khẩu hiện tại (bỏ trống nếu không
                                            đổi)</label>
                                        <input id="current_password" name="current_password" type="password" ng-model="form.current_password"
                                               autocomplete="current-password">
                                        <div class="invalid-feedback d-block" ng-if="errors['current_password']">
                                            <% errors['current_password'][0] %>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="new_password">Mật khẩu mới (bỏ trống nếu không đổi)</label>
                                        <input id="new_password" name="new_password" type="password" ng-model="form.new_password"
                                               autocomplete="new-password">
                                        <div class="invalid-feedback d-block" ng-if="errors['new_password']">
                                            <% errors['new_password'][0] %>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm_password">Xác nhận mật khẩu mới</label>
                                        <input id="confirm_password" name="confirm_password" type="password" ng-model="form.new_password_confirmation"
                                               autocomplete="new-password">
                                        <div class="invalid-feedback d-block" ng-if="errors['new_password_confirmation']">
                                            <% errors['new_password_confirmation'][0] %>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button class="btn btn-primary"
                                            type="button"
                                            ng-click="submitInfo()"
                                            ng-disabled="loading"
                                            aria-busy="<% loading %>">
                                        <span ng-if="!loading"><i class="fas fa-save me-1"></i> Lưu thay đổi</span>
                                        <span ng-if="loading"><i class="fas fa-spinner fa-spin me-1"></i> Đang lưu…</span>
                                    </button>

                                </div>
                            </form>
                        </div>

                        <!-- Tab: Đơn hàng -->
                        <div class="panel " id="orders" role="tabpanel" >
                            <h3 class="panel-title">Quản lý bài viết</h3>
                            <!-- Demo table; thay bằng loop đơn hàng của bạn -->
                            <div class="table-wrap">
                                <table class="table-orders">
                                    <thead>
                                    <tr>
                                        <th>Bài viết</th>
                                        <th>File đính kèm</th>
                                        <th>Ngày gửi</th>
                                        <th>Trạng thái</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($blogs ?? [] as $item)
                                        @php
                                            $rawHtml  = (string) ($item->content_html ?? '');

                                            // 1) Giải mã HTML entities -> UTF-8 (bao gồm &nbsp;)
                                            $decoded  = html_entity_decode($rawHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                                            // 2) Loại thẻ HTML
                                            $plain    = strip_tags($decoded);

                                            // 3) Chuẩn hoá khoảng trắng (bao gồm non-breaking space U+00A0, &nbsp;)
                                            $plain    = preg_replace('/\x{00A0}/u', ' ', $plain);   // thay NBSP bằng space
                                            $plain    = preg_replace('/\s+/u', ' ', $plain);
                                            $plain    = trim($plain);

                                            // 4) Cắt độ dài mong muốn
                                            $excerpt  = Str::limit($plain, 60, '…');

                                        // Map trạng thái
                                        $statusMap = [
                                            'pending'  => ['label' => 'Đang chờ duyệt', 'class' => 'badge--pending'],
                                            'approved' => ['label' => 'Đã duyệt',        'class' => 'badge--approved'],
                                            'rejected' => ['label' => 'Bị từ chối',      'class' => 'badge--rejected'],
                                        ];
                                        $st = $statusMap[$item->status ?? 'pending'] ?? $statusMap['pending'];

                                        // File đính kèm
                                        $fileUrl  = $item->attachment_path ? asset($item->attachment_path) : null;
                                        $fileName = $item->attachment_name ?: ($item->attachment_path ? basename($item->attachment_path) : null);
                                        @endphp


                                        <tr>
                                            <td class="td-title" data-th="Bài viết">
                                                <div class="post-title">{{ $item->title }}</div>
                                                <div class="post-author text-muted">Tác giả: {{ $item->author_name }}</div>
                                            </td>



                                            <td class="td-file" data-th="File đính kèm">
                                                @if($fileUrl)
                                                    <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="file-link">📄 {{ $fileName }}</a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            <td class="td-date" data-th="Ngày gửi">
                                                {{ optional($item->created_at)->format('d/m/Y H:i') }}
                                            </td>

                                            <td class="td-status" data-th="Trạng thái">
                                                <span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span>
                                            </td>

                                            <td class="td-actions">
                                                <a href="" class="btn-view"
                                                   data-id="{{ $item->id }}"
                                                   data-title="{{ e($item->title) }}"
                                                   data-date="{{ optional($item->created_at)->format('d/m/Y H:i') }}"
                                                   data-status="{{ $item->status }}"
                                                   data-file-url="{{ $fileUrl }}"
                                                   data-file-name="{{ $fileName }}"
                                                   data-admin-comment="{{ e($item->review_note ?? '') }}"
                                                >
                                                    Xem
                                                </a>

                                                <div id="html-{{ $item->id }}" class="js-content-html" hidden>
                                                    {!! $item->content_html !!}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if(empty($blogs) || count($blogs)===0)
                                        <tr>
                                            <td colspan="5">Chưa có bài viết nào.</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>

                            <style>
                                #orders .orders-pager{
                                    display:flex;
                                    justify-content:center;
                                    margin:16px 0 4px;
                                }
                            </style>

                            @if(!empty($blogs) && method_exists($blogs, 'links'))
                                <div class="orders-pager">
                                    {{ $blogs->fragment('orders')->links('site.pagination.paginate2') }}
                                </div>
                            @endif
                        </div>
                    </main>
                </div>
            </div>
        </section>

        <style>
            :root {
                --acc-bg: #fff;
                --acc-border: #e8edf2;
                --acc-muted: #6b7280;
                --acc-primary: #ff6a00; /* bạn đổi theo brand */
                --acc-ring: rgba(255, 106, 0, .15);
                --radius: 14px;
                --shadow: 0 6px 20px rgba(0, 0, 0, .06);
                --gap: 22px;
            }

            .account-grid {
                display: grid;
                grid-template-columns:280px 1fr;
                gap: var(--gap);
            }

            .account-aside {
                position: sticky;
                top: 24px;
                height: max-content
            }

            .user-card {
                background: var(--acc-bg);
                border: 1px solid var(--acc-border);
                border-radius: var(--radius);
                padding: 18px;
                box-shadow: var(--shadow);
                text-align: center
            }

            .avatar-user {
                position: relative;
                width: 104px;
                height: 104px;
                margin: 8px auto 12px
            }

            .avatar-user img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 50%;
                border: 4px solid #f3f6f9
            }

            .avatar-edit {
                position: absolute;
                right: -4px;
                bottom: -4px;
                border: 0;
                background: #fff;
                border-radius: 999px;
                padding: 8px;
                box-shadow: var(--shadow);
                cursor: pointer
            }

            .user-info .name {
                display: block;
                margin-bottom: 2px
            }

            .user-info .email {
                display: block;
                color: var(--acc-muted);
                font-size: .92rem;
                margin-bottom: 10px
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                border-radius: 12px;
                padding: 10px 14px;
                border: 1px solid transparent;
                cursor: pointer;
                transition: .2s
            }

            .btn-ghost {
                background: #fff;
                border-color: var(--acc-border);
                color: #111
            }

            .btn-ghost:hover {
                border-color: #cfd7df
            }

            .btn-primary {
                background: var(--acc-primary);
                color: #fff
            }

            .btn-primary:hover {
                filter: brightness(.95)
            }

            .account-menu {
                margin-top: 14px;
                background: var(--acc-bg);
                border: 1px solid var(--acc-border);
                border-radius: var(--radius);
                box-shadow: var(--shadow);
                overflow: hidden
            }

            .account-menu .menu-item {
                display: flex;
                gap: 10px;
                align-items: center;
                padding: 12px 16px;
                color: #111;
                border-left: 3px solid transparent
            }

            .account-menu .menu-item + .menu-item {
                border-top: 1px dashed var(--acc-border)
            }

            .account-menu .menu-item:hover {
                background: #fafbfc
            }

            .account-menu .menu-item.is-active {
                background: linear-gradient(0deg, rgba(255, 106, 0, .06), rgba(255, 106, 0, .06));
                border-left-color: var(--acc-primary)
            }

            .mi-icon {
                width: 20px;
                display: inline-block;
                text-align: center;
                opacity: .8
            }

            .account-main .panel {
                display: none;
                background: var(--acc-bg);
                border: 1px solid var(--acc-border);
                border-radius: var(--radius);
                box-shadow: var(--shadow);
                padding: 18px
            }

            .account-main .panel.is-active {
                display: block
            }

            .panel-title {
                margin: 4px 0 14px
            }

            .panel-subtitle {
                margin: 10px 0 10px;
                color: #111
            }

            .form-styled input {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid var(--acc-border);
                border-radius: 12px;
                outline: none
            }

            .form-styled input:focus {
                border-color: var(--acc-primary);
                box-shadow: 0 0 0 4px var(--acc-ring)
            }

            .form-styled label {
                display: block;
                margin: 0 0 6px;
                font-weight: 600
            }

            .form-styled .hint {
                display: block;
                margin-top: 6px;
                color: var(--acc-muted);
                font-size: .9rem
            }

            .form-styled .req {
                color: #ef4444
            }

            .form-grid {
                display: grid;
                grid-template-columns:1fr 1fr;
                gap: 14px
            }

            .form-group.col-span-2 {
                grid-column: 1/-1
            }

            .form-actions {
                margin-top: 6px
            }



            /* Responsive */
            @media (max-width: 992px) {
                .account-grid {
                    grid-template-columns:1fr
                }

                .account-aside {
                    position: static
                }
            }

            @media (max-width: 640px) {
                .account-menu .menu-item {
                    padding: 12px
                }
            }
        </style>

        <style>
            .table-wrap{
                overflow-x:auto;           /* cho phép scroll ngang */
                -webkit-overflow-scrolling:touch; /* mượt trên iOS */
                border:1px solid var(--acc-border);
                border-radius:10px;
            }

            /* gợi ý người dùng có thể kéo (fade 2 bên) */
            .table-wrap {
                background:
                    linear-gradient(to right, rgba(0,0,0,.08), rgba(0,0,0,0)) left/16px 100% no-repeat,
                    linear-gradient(to left, rgba(0,0,0,.08), rgba(0,0,0,0)) right/16px 100% no-repeat;
                background-attachment:local, local;
            }

            .table-orders{
                width:100%;
                border-collapse:separate;
                border-spacing:0;
                /* ép tối thiểu để xuất hiện scroll ngang khi cần */
                min-width:720px; /* chỉnh theo nội dung thực tế */
            }

            .table-orders th,
            .table-orders td{
                padding:12px 10px;
                border-bottom:1px solid var(--acc-border);
                text-align:left;
                vertical-align:top;
            }

            .table-orders thead th{
                background:#fafbfc;
                font-weight:700;
                position:sticky; top:0; /* sticky header khi scroll */
                z-index:1;
            }

            /* tinh chỉnh mỗi cột để gọn hơn */
            .td-title .post-title{font-weight:600; line-height:1.35;}
            .td-title .post-title{
                display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
            }
            .td-title .post-author{font-size:12px;}

            .td-file .file-link{white-space:nowrap; text-overflow:ellipsis; overflow:hidden; display:inline-block; max-width:220px;}
            .td-date{white-space:nowrap;}
            .td-status .badge{font-size:12px; padding:6px 8px; border-radius:6px;}
            .td-actions .btn-view{
                display:inline-block; padding:6px 10px; border:1px solid var(--acc-border);
                border-radius:8px; text-decoration:none;
            }

            /* ====== Chế độ “card” cho màn <576px ====== */
            @media (max-width:575.98px){
                .table-orders{
                    min-width:0;        /* bỏ ép min-width để không cần kéo ngang */
                    border-collapse:separate;
                }
                .table-orders thead{display:none;} /* ẩn header, dùng ::before */
                .table-orders tbody tr{
                    display:block;
                    border-bottom:1px solid var(--acc-border);
                    padding:10px 12px;
                }
                .table-orders tbody tr:last-child{border-bottom:none;}

                .table-orders td{
                    display:grid;
                    grid-template-columns:120px 1fr; /* label | value */
                    padding:8px 0;
                    border-bottom:none;
                }
                .table-orders td::before{
                    content:attr(data-th);
                    font-weight:600;
                    color:#555;
                    padding-right:10px;
                }

                /* cột tiêu đề gộp toàn dòng cho đẹp */
                .table-orders .td-title{
                    display:block;
                    padding-top:0;
                }
                .table-orders .td-title::before{content:none;}
                .table-orders .td-title .post-title{font-size:15px; -webkit-line-clamp:3;}
                .table-orders .td-title .post-author{margin-top:4px;}

                /* nút hành động full width trên mobile */
                .table-orders .td-actions{
                    grid-template-columns:1fr;
                }
                .table-orders .td-actions .btn-view{
                    text-align:center; width:100%;
                }

                /* link file bọc dòng */
                .td-file .file-link{max-width:100%; white-space:normal;}
            }
        </style>


        <div class="sec-dec"></div>
        <!--about end   -->


        <style>
            /* Modal base */
            .sa-modal{ position:fixed; inset:0; display:none; z-index:9999; }
            .sa-modal.is-open{ display:block; }
            .sa-modal__overlay{ position:absolute; inset:0; background:rgba(0,0,0,.45); }
            .sa-modal__dialog{
                position:relative; margin: 220px auto; background:#fff; border-radius:16px;
                max-width: 1080px; width: calc(100% - 24px); max-height: calc(100vh - 80px);
                display:flex; flex-direction:column; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.25);
            }
            .sa-modal__dialog--xl{ max-width: 1100px; }
            .sa-modal__header{ padding:16px 20px; border-bottom:1px solid #eee; display:flex; align-items:center; justify-content:space-between; }
            .sa-modal__title{ margin:0; font-size:20px; font-weight:800; }
            .sa-modal__close{ border:none; background:transparent; font-size:28px; line-height:1; cursor:pointer; padding:4px 8px; }
            .sa-modal__body{ padding:16px 20px; overflow:auto; }
            .sa-modal__footer{ padding:16px 20px; border-top:1px solid #eee; text-align:right; }

            /* Content blocks */
            .sa-block{ margin-bottom:16px; }
            .sa-block__title{ font-weight:700; margin-bottom:8px; }
            .sa-meta{ display:flex; gap:24px; flex-wrap:wrap; margin-bottom:10px; }

            /* Grid */
            .sa-grid-2{ display:grid; grid-template-columns:1fr 1fr; gap:16px; }
            @media (max-width: 768px){ .sa-grid-2{ grid-template-columns:1fr; } }

            /* Button */
            .sa-btn{
                background:#5F27CD; color:#fff; border:none; padding:10px 16px; border-radius:10px;
                font-weight:700; cursor:pointer;
            }

            /* Badge trạng thái */
            .badge { display:inline-block; padding:4px 8px; border-radius:999px; font-size:12px; font-weight:700; border:1px solid transparent; }
            .badge--pending{  background:#fff7ed; color:#c2410c; border-color:#fed7aa; }
            .badge--approved{ background:#ecfdf5; color:#047857; border-color:#a7f3d0; }
            .badge--rejected{ background:#fef2f2; color:#b91c1c; border-color:#fecaca; }

            .text-muted{ color:#6b7280; }

            /* CKEditor content styling (tối giản) */
            .ck-content{ line-height:1.7; }
            .ck-content h1,.ck-content h2,.ck-content h3{ margin: .8em 0 .4em; }
            .ck-content p{ margin: .6em 0; }
            .ck-content ul, .ck-content ol{ margin: .6em 1.2em; }
            .ck-content img{ max-width:100%; height:auto; border-radius:8px; }
        </style>

        <div id="viewModal" class="sa-modal" aria-hidden="true" role="dialog" aria-modal="true">
            <div class="sa-modal__overlay" data-close-modal></div>
            <div class="sa-modal__dialog sa-modal__dialog--xl" role="document" tabindex="-1">
                <div class="sa-modal__header">
                    <h3 id="mTitle" class="sa-modal__title">Tiêu đề</h3>
                    <button type="button" class="sa-modal__close" title="Đóng" data-close-modal>&times;</button>
                </div>

                <div class="sa-modal__body">
                    <div class="sa-meta">
                        <div><strong>Ngày gửi:</strong> <span id="mDate">—</span></div>
                        <div><strong>Trạng thái:</strong> <span id="mStatus" class="">—</span></div>
                    </div>

                    <div class="sa-block">
                        <div class="sa-block__title">Nội dung</div>
                        <!-- dùng ck-content để có style giống CKEditor -->
                        <div id="mContent" class="ck-content"></div>
                    </div>

                    <div class="sa-grid-2">
                        <div class="sa-block">
                            <div class="sa-block__title">File đính kèm</div>
                            <div id="mFile">—</div>
                        </div>
                        <div class="sa-block">
                            <div class="sa-block__title">Ghi chú từ admin</div>
                            <div id="mAdminComment" class="text-muted">—</div>
                        </div>
                    </div>
                </div>

                <div class="sa-modal__footer">
                    <button type="button" class="sa-btn" data-close-modal>Đóng</button>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // Tabs: click menu to show panel
        document.addEventListener('click', function (e) {
            const link = e.target.closest('.account-menu .menu-item[data-tab]');
            if (!link) return;
            e.preventDefault();
            const tab = link.dataset.tab;

            // active menu
            document.querySelectorAll('.account-menu .menu-item').forEach(a => a.classList.toggle('is-active', a === link));
            // active panel
            document.querySelectorAll('.account-main .panel').forEach(p => p.classList.toggle('is-active', p.id === tab));
        });

        // support open by hash
        window.addEventListener('load', () => {
            const hash = location.hash.replace('#', '');
            if (!hash) return;
            const link = document.querySelector(`.account-menu .menu-item[data-tab="${hash}"]`);
            if (link) {
                link.click();
            }
        });
    </script>
    <script>
        app.controller('profilePage', function ($rootScope, $scope, $interval) {
            $scope.form = @json($customer);

            $scope.avatarPreviewUrl = window.USER_AVATAR_URL;
            $scope.form.avatar = null;
            $scope.pickAvatar = function () {
                document.getElementById('avatar-input').click();
            };

            $scope.$watch('form.avatar', function (newFile) {
                if (!newFile) return;

                const isImage = newFile.type ? newFile.type.startsWith('image/') : /\.(png|jpe?g|gif|webp|bmp|svg)$/i.test(newFile.name || '');
                if (!isImage) {
                    alert('Vui lòng chọn tệp hình ảnh');
                    $scope.form.avatar = null;
                    return;
                }
                const MAX = 5 * 1024 * 1024; // 5MB
                if (newFile.size > MAX) {
                    alert('Ảnh vượt quá 5MB, vui lòng chọn ảnh nhỏ hơn.');
                    $scope.form.avatar = null;
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    $scope.$apply(() => {
                        $scope.avatarPreviewUrl = e.target.result; // hiển thị xem trước
                    });
                };
                reader.readAsDataURL(newFile);
            });



            $scope.submitInfo = function () {
                if ($scope.loading) return;

                $scope.loading = true;
                var fd = new FormData();
                fd.append('fullname', $scope.form.fullname);
                fd.append('email',    $scope.form.email);
                fd.append('current_password',    $scope.form.current_password ?? '');
                fd.append('new_password',    $scope.form.new_password ?? '' );
                fd.append('new_password_confirmation',    $scope.form.new_password_confirmation ?? '');

                if ($scope.form.avatar) {
                    fd.append('avatar', $scope.form.avatar);
                }

                $.ajax({
                    type: 'POST',
                    url:  "{{ route('front.updateProfile') }}",
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
                            // setTimeout(function() {
                            //     window.location.reload();
                            // }, 1000);
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

            $('.btn-logout').on('click', function(e){
                e.preventDefault();

                $.ajax({
                    url: '{{ route("front.logout") }}',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN
                    },
                    success: function() {
                        window.location.href = '{{ route("front.home-page") }}';
                    },
                    error: function() {
                        window.location.href = '{{ route("front.home-page") }}';
                    }
                });
            });
        })
    </script>
    <script>
        (function(){
            const modal = document.getElementById('viewModal');
            const elTitle  = document.getElementById('mTitle');
            const elDate   = document.getElementById('mDate');
            const elStatus = document.getElementById('mStatus');
            const elContent= document.getElementById('mContent');
            const elFile   = document.getElementById('mFile');
            const elCmt    = document.getElementById('mAdminComment');

            const statusMap = {
                'pending':  { label: 'Đang chờ duyệt', cls: 'badge--pending'  },
                'approved': { label: 'Đã duyệt',        cls: 'badge--approved' },
                'rejected': { label: 'Bị từ chối',      cls: 'badge--rejected' },
            };

            function openModal(){ modal.classList.add('is-open'); trapFocus(); }
            function closeModal(){ modal.classList.remove('is-open'); releaseFocus(); }

            // Close handlers
            modal.addEventListener('click', function(e){
                if (e.target.matches('[data-close-modal]')) closeModal();
            });
            document.addEventListener('keydown', function(e){
                if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
            });

            // Delegate click on .btn-view
            document.addEventListener('click', function(e){
                const a = e.target.closest('.btn-view');
                if (!a) return;
                e.preventDefault();

                const id     = a.getAttribute('data-id');
                const title  = a.getAttribute('data-title') || '—';
                const date   = a.getAttribute('data-date')  || '—';
                const status = (a.getAttribute('data-status') || 'pending').toLowerCase();
                const fileUrl= a.getAttribute('data-file-url') || '';
                const fileNm = a.getAttribute('data-file-name') || '';
                const adminC = a.getAttribute('data-admin-comment') || '';

                // Title, date
                elTitle.textContent = title;
                elDate.textContent  = date;

                // Status
                const map = statusMap[status] || statusMap['pending'];
                elStatus.textContent = map.label;

                // Content HTML (raw) lấy từ kho ẩn
                const store = document.getElementById('html-' + id);
                elContent.innerHTML = store ? store.innerHTML : '<em class="text-muted">—</em>';

                // File
                if (fileUrl) {
                    elFile.innerHTML = `<a href="${fileUrl}" target="_blank" rel="noopener">📄 ${escapeHtml(fileNm || fileUrl)}</a>`;
                } else {
                    elFile.textContent = '—';
                }

                // Admin comment
                elCmt.textContent = adminC || '—';

                openModal();
            });

            // Simple focus trap for accessibility
            let lastFocused = null;
            function trapFocus(){
                lastFocused = document.activeElement;
                const dialog = modal.querySelector('.sa-modal__dialog');
                dialog.setAttribute('tabindex','-1');
                dialog.focus();
            }
            function releaseFocus(){
                if (lastFocused && typeof lastFocused.focus === 'function') lastFocused.focus();
            }

            // Escape HTML for file name
            function escapeHtml(s){
                return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
            }
        })();
    </script>

@endpush
