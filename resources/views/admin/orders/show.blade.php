@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
    Chi tiết bài viết đóng góp
@endsection

@section('title')
    Chi tiết bài viết đóng góp
@endsection

@section('buttons')
@endsection

@section('content')

<div ng-controller="Order" ng-cloak>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6>Thông tin bài viết</h6>
                </div>
                <div class="card-body">
                    <form class="article-show">
                        <!-- Thanh tiêu đề dính trên cùng -->
                        <div class="card shadow-sm mb-3 sticky-top" style="top: 70px; z-index: 1010;">
                            <div class="card-body d-flex flex-wrap align-items-center gap-2">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small">Bài viết:</span>
                                        <strong class="h6 mb-0 text-truncate"><% form.title %></strong>
                                    </div>
                                    <div class="small text-muted">
                                        Gửi lúc: <% form.created_at | date:'dd/MM/yyyy HH:mm' %>
                                        • CTV: <% form.customer.fullname %> (<% form.customer.code %>)
                                    </div>
                                </div>


                            </div>
                        </div>

                        <!-- Thân nội dung -->
                        <div class="row g-3">
                            <!-- Cột trái: Nội dung & đính kèm -->
                            <div class="col-lg-8">
                                <div class="card mb-3">
                                    <div class="card-header py-2">
                                        <h6 class="mb-0">Nội dung bài viết</h6>
                                    </div>
                                    <div class="card-body">

                                        <div class="ck-content" ng-bind-html="trustAsHtml(form.content_html)"></div>

                                        <!-- Fallback khi chưa có nội dung -->
                                        <div class="text-muted small" ng-if="!form.content_html">
                                            (Chưa có nội dung)
                                        </div>

{{--                                        <textarea   class="form-control"--}}
{{--                                                  ck-editor  ng-model="form.content_html" rows="14"></textarea>--}}

                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header py-2 d-flex align-items-center justify-content-between">
                                        <h6 class="mb-0">Tệp đính kèm</h6>
                                        <span class="text-muted small">
      <% form.attachment_path ? 1 : 0 %> tệp
    </span>
                                    </div>

                                    <div class="card-body">
                                        <!-- Có tệp -->
                                        <div class="list-group list-group-flush" ng-if="form.attachment_path">
                                            <div class="list-group-item d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="ratio ratio-1x1 rounded bg-light d-inline-flex align-items-center justify-content-center" style="width:42px;">
                                                        <i class="fa"
                                                           class="fa-file-word-o"></i>
                                                    </div>

                                                    <div>
                                                        <div class="fw-semibold text-truncate" style="max-width: 360px;">
                                                            <% form.attachment_name || 'Tệp đính kèm' %>
                                                        </div>
                                                        <div class="small text-muted text-truncate" style="max-width: 360px;">
                                                            <% form.attachment_path %>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center gap-2">
                                                    <a class="btn btn-outline-primary btn-sm"
                                                       ng-href="<% attachmentUrl() %>"
                                                       download="<% form.attachment_name || document %>">
                                                        <i class="fa fa-download me-1"></i>Tải xuống
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Không có tệp -->
                                        <div class="text-muted small" ng-if="!form.attachment_path">
                                            Chưa có tệp đính kèm.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cột phải: Thông tin meta -->
                            <div class="col-lg-4">
                                <div class="card mb-3">
                                    <div class="card-header py-2">
                                        <h6 class="mb-0">Thông tin bài viết</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Tiêu đề</label>
                                            <div class="form-control-plaintext fw-semibold text-break"><% form.title %></div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Ngày gửi</label>
                                            <div class="form-control-plaintext"><% form.created_at | date:'dd/MM/yyyy HH:mm' %></div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Cộng tác viên</label>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <div class="form-control-plaintext">
                                                        <% form.customer.fullname %> – <% form.customer.code %>
                                                    </div>
                                                    <div class="form-control-plaintext text-muted small">
                                                        <i class="fa fa-envelope-o me-1"></i><% form.customer.email %>
                                                    </div>
                                                </div>
                                                <a ng-if="form.customer_id"
                                                   ng-href="/admin/customers/<% form.customer_id %>/show"
                                                   class="ms-2 text-primary" title="Xem chi tiết CTV">
                                                    <i class="fa fa-user-circle fa-lg"></i>
                                                </a>
                                            </div>
                                        </div>


                                        <div class="mb-2">
                                            <label class="form-label small text-muted">Trạng thái</label>
                                            <div class="d-flex align-items-center gap-2">
    <span class="badge"
          ng-class="{
            'bg-warning text-dark': form.status=='pending',
            'bg-success': form.status=='approved',
            'bg-danger': form.status=='rejected',
            'bg-secondary': ['pending','approved','rejected'].indexOf(form.status)===-1
          }">
      <% (statusMap[form.status] || 'Không rõ') %>
    </span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Cập nhật trạng thái</label>
                                            <select class="form-select form-select-sm" ng-model="form.status">
                                                <option ng-repeat="s in statuses" ng-value="s.id"><% s.name %></option>
                                            </select>
                                        </div>

                                        <!-- Ô review của admin -->
                                        <div class="mb-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="form-label small text-muted mb-1">Review của admin</label>
                                                <small class="text-muted"><% (form.review_note || '').length %>/1000</small>
                                            </div>

                                            <textarea class="form-control"
                                                      rows="4"
                                                      maxlength="1000"
                                                      ng-model="form.review_note"
                                                      placeholder="Nhập nhận xét/ghi chú cho cộng tác viên (tối đa 1000 ký tự)"
                                                      ng-change="autoGrow($event)"
                                                      ng-disabled="+form.is_comment === 1"
                                                      ng-attr-title="<% (+form.is_comment === 1) ? 'Đã khóa: không cho nhập' : '' %>">
</textarea>


                                            <div class="d-grid mt-2">
                                                <button type="button" class="btn btn-success btn-sm" ng-click="submit(1)">
                                                    <i class="fa fa-save me-1"></i>Lưu trạng thái
                                                </button>

                                                <button type="button" class="btn btn-primary btn-sm" ng-click="submit(2)">
                                                    <i class="fa fa-save me-1"></i>Lưu trạng thái & review
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>

    </div>





</div>
@endsection

@section('script')
    @include('admin.orders.Order')
    <script>
        window.APP_BASE_URL = "{{ url('/') }}"; // ví dụ: https://yourdomain.com
    </script>
    <script>
        app.controller('Order', function ($scope, $http, $window) {
            const BASE = ($window.APP_BASE_URL || $window.location.origin || '').replace(/\/+$/,'');

            function isAbsolute(u){ return /^(https?:)?\/\//i.test(u || ''); }

            function joinUrl(base, path){
                if (!path) return '';
                if (isAbsolute(path)) return path; // đã là URL đầy đủ
                const clean = String(path).replace(/^\/+/, ''); // bỏ / đầu
                return base + '/' + clean;
            }

            $scope.attachmentUrl = function(){
                return encodeURI( joinUrl(BASE, $scope.form.attachment_path) );
            };

            $scope.form = new Order(@json($order), {scope: $scope});
            $scope.statuses = [
                { id: 'pending',  name: 'Chờ duyệt' },
                { id: 'approved', name: 'Đã duyệt'  },
                { id: 'rejected', name: 'Từ chối'   },
            ];
            $scope.statusMap = {
                pending:  'Chờ duyệt',
                approved: 'Đã duyệt',
                rejected: 'Từ chối',
            };


            $scope.loading = {};
            $scope.submit = function (send = 1) {
                $scope.loading.submit = true;
                let data = {
                    article_id: $scope.form.id,
                    status: $scope.form.status,
                    review_note: $scope.form.review_note,
                    send: send,
                };

                $.ajax({
                    type: 'POST',
                    url: "/admin/article-submissions/update-status",
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = "{{ route('orders.index') }}";
                        } else {
                            toastr.warning(response.message);
                            $scope.errors = response.errors;
                        }
                    },
                    error: function (e) {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function () {
                        $scope.loading.submit = false;
                        $scope.$applyAsync();
                    }
                });
            }

        });
    </script>
@endsection
