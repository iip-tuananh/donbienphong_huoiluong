<style>
	#notification-list {
		max-height: 400px;
		overflow-y: auto;
	}

	.notification-item {
		cursor: pointer;
	}

	.notification-item:not(:last-child) {
		border-bottom: 1px solid #ccc;
	}

	.notification-item:hover {
		background-color: rgb(185 217 253) !important;
	}

	.unread {
		background-color: rgb(225, 239, 255);
	}

	.notification-sender img {
		width: 45px;
		height: 45px;
	}

	.notification-time {
		font-size: 0.8em;
		font-style: italic;
	}
</style>
<nav class="main-header navbar navbar-expand navbar-white navbar-light" ng-controller="Notification">
	<!-- Left navbar links -->
	<ul class="navbar-nav">
		<li class="nav-item mr-4">
			<a class="nav-link icon-menu" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars" style="color: #F58220 !important"></i></a>
		</li>
		{{-- @if(Auth::user()->type == \App\Model\Common\User::G7 || Auth::user()->type == \App\Model\Common\User::NHAN_VIEN_G7)
		<li class="nav-item d-none d-sm-inline-block mr-3">
			<a href="{{ route('Bill.create') }}" class="btn btn-success btn-block btn-quick"><i class="fa fa-shopping-cart"></i> Lập hóa đơn</a>
		</li>
		<li class="nav-item d-none d-sm-inline-block mr-3">
			<a href="{{ route('ReceiptVoucher.index') }}" class="btn btn-primary btn-block btn-quick"><i class="fa fa-hand-holding-usd"></i> Lập phiếu thu</a>
		</li>
		<li class="nav-item d-none d-sm-inline-block">
			<a href="{{ route('PaymentVoucher.index') }}" class="btn btn-warning btn-block btn-quick"><i class="fa fa-hand-holding-usd"></i> Lập phiếu chi</a>
		</li>
		@endif --}}
	</ul>
	<!-- SEARCH FORM -->
	<!-- Right navbar links -->
	<ul class="navbar-nav ml-auto">
		<!-- Notifications Dropdown Menu -->
		<li class="nav-item dropdown" ng-cloak>
			<a class="nav-link" data-toggle="dropdown" href="#">
				<i class="far fa-bell" style="font-size: 32px !important;"></i>
				<span class="badge badge-warning navbar-badge" style="font-size: 16px"><% unread %></span>
			</a>
			<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
				<span class="dropdown-item dropdown-header">Thông báo</span>
				<div class="dropdown-divider"></div>
				<div id="notification-list">
					<div ng-if="!notifications.length" class="p-3 text-center">
						Không có thông báo
					</div>
					<div class="notification-item d-flex" ng-repeat="n in notifications" ng-class="{unread: n.status == 0}" ng-click="showNotification(n)">
						<div class="notification-sender px-2 my-auto" title="<% n.sender_name %>">
							<img ng-src="<% n.sender_avatar || '/img/avatar.png' %>">
						</div>
						<div class="notification-content p-2">
							<div ng-bind-html="trustAsHtml(n.content)"></div>
							<div class="notification-time"><% calculateTime(n.created_at) %></div>
						</div>
					</div>
					<div ng-if="loading" class="p-2 text-center">
						<i class="fa fa-spinner fa-spin"></i> Đang tải dữ liệu
					</div>
				</div>
				<div class="dropdown-divider"></div>
			</div>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('logout') }}">
				<i class="fas fa-sign-out-alt"></i>
			</a>
		</li>
	</ul>
</nav>
<audio class="d-none" src="{{ asset('audio/notification.mp3') }}" id="notification-audio"></audio>
<script src="{{ URL::asset('libs/socket/socket.io.js') }}"></script>
<script>
	const SOCKET_URL = "{{ env('SOCKET_URL') ?? '' }}";
	var token = localStorage.getItem("{{ env('prefix') }}-token");
    if(SOCKET_URL) {
        var socket = io.connect(SOCKET_URL, {
            query: "token=" + token
        });
	}
	let audio = document.getElementById('notification-audio');

	app.controller('Notification', function ($scope, $http, $sce) {
		$scope.unread = 0;
		$scope.notifications = [];
		let hasMore = true;

		getMoreNotification();

		if(socket) {
			socket.on('notification', function(data) {
				data = JSON.parse(data);
				$scope.notifications.unshift(data);
				$scope.unread++;
				audio.currentTime = 0;
				audio.play();
				toastr.success(data.content);
				$scope.$applyAsync();
			})
		}

		$scope.showNotification = function(notification) {
			var url = `/common/notifications/${notification.id}/read`;
			$.ajax({
				url: url,
				type: 'GET',
				success: function(response) {
					if (response.success) {
						window.location.href = notification.url;
					} else {
						toastr.warning(response.message);
					}
				}
			});
		}

		$scope.trustAsHtml = function(html) {
			return $sce.trustAsHtml(html);
		}

		function getMoreNotification() {
			$scope.loading = true;
			$scope.$applyAsync();
			$.ajax({
				url: "{{ route('Notification.searchData') }}?skip=" + $scope.notifications.length,
				type: 'GET',
				success: function(response) {
					if (response.success) {
						$scope.unread = response.data.unread;
						$scope.notifications = $scope.notifications.concat(response.data.notifications);
						if (!response.data.notifications.length) hasMore = false;
					} else {
						toastr.warning(response.message);
					}
					$scope.loading = false;
					$scope.$applyAsync();
				}
			});
		}

		$('#notification-list').on('scroll', function() {
			if($(this).scrollTop() + $(this).innerHeight() + 20 >= $(this)[0].scrollHeight) {
				if (hasMore && !$scope.loading) getMoreNotification();
			}
		})
	})
</script>
</nav>
