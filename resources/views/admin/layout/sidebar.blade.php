<!-- Sidebar -->
            <div class="sidebar" id="sidebar">
                <div class="sidebar-inner slimscroll">
					<div id="sidebar-menu" class="sidebar-menu">
						<ul>
							<li class="menu-title"> 
								<span>Main</span>
							</li>
							<li class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
								<a href="{{ route('superadmin.dashboard') }}"><i class="fe fe-home"></i> <span>Dashboard</span></a>
							</li>

							<!-- <li class="">
								<a href=""><i class="fe fe-layout"></i> <span>Appointments</span></a>
							</li> -->

							<!-- <li class="">
								<a href=""><i class="fe fe-users"></i> <span>Specialities</span></a>
							</li> -->

							

							

							<li class="{{ request()->routeIs('superadmin.user.list.page') ? 'active' : '' }}">
								<a href="{{ route('superadmin.user.list.page') }}"><i class="fe fe-user"></i> <span>Users</span></a>
							</li>
							<li class="{{ request()->routeIs('superadmin.category.list.page') ? 'active' : '' }}">
								<a href="{{ route('superadmin.category.list.page') }}"><i class="fe fe-folder"></i> <span>Video Categories</span></a>
							</li>
							<li class="{{ request()->routeIs('superadmin.video.list.page') ? 'active' : '' }}">
								<a href="{{ route('superadmin.video.list.page') }}"><i class="fe fe-video"></i> <span>Videos</span></a>
							</li>
							<li class="{{ request()->routeIs('superadmin.subscriptions.index') ? 'active' : '' }}">
								<a href="{{ route('superadmin.subscriptions.index') }}"><i class="fe fe-gift"></i> <span>Subscriptions</span></a>
							</li>
							<li class="{{ request()->routeIs('superadmin.wallet.requests') ? 'active' : '' }}">
								<a href="{{ route('superadmin.wallet.requests') }}"><i class="fe fe-credit-card"></i> <span>Withdrawal Requests</span></a>
							</li>
							<li class="{{ request()->routeIs('superadmin.notification.list.page') ? 'active' : '' }}">
								<a href="{{ route('superadmin.notification.list.page') }}"><i class="fe fe-bell"></i> <span>Notification Template</span></a>
							</li>

							
							<li class="{{ request()->routeIs('superadmin.setting') ? 'active' : '' }}">
								<a href="{{ route('superadmin.setting') }}"><i class="fe fe-gear"></i> <span>Settings</span></a>								
							</li>

							<li class="{{ request()->routeIs('superadmin.logout') ? 'active' : '' }}">
								<a href="{{ route('superadmin.logout') }}"><i class="fe fe-bell"></i> <span>LOG OUT</span></a>								
							</li>


							<!-- <li class="{{ request()->routeIs('superadmin.call.reports') ? 'active' : '' }}">
								<a href="{{ route('superadmin.call.reports') }}"><i class="fe fe-document"></i><span> Call Reports</span></a>
							</li> -->

							<!-- <li class="submenu {{ request()->routeIs('superadmin.pricing.*') }}">
								<a href="#" class="{{ request()->routeIs('superadmin.pricing.*') ? 'subdrop' : '' }}">
									<i class="fe fe-layout"></i> 
									<span> Control Pricing </span> 
									<span class="menu-arrow"></span>
								</a>
								<ul style="{{ request()->routeIs('superadmin.pricing.*') ? 'display: block;' : 'display: none;' }}">
									<li class="{{ request()->routeIs('superadmin.pricing.list') ? 'active' : '' }}">
										<a href="{{ route('superadmin.pricing.list') }}"><i class="fa-regular fa-circle" style="font-size: 9px;"></i> List</a>
									</li>
									<li class="{{ request()->routeIs('superadmin.pricing.add') ? 'active' : '' }}">
										<a href="{{ route('superadmin.pricing.add') }}"><i class="fa-regular fa-circle" style="font-size: 9px;"></i> Add</a>
									</li>
								</ul>
							</li> -->

							<!-- <li> 
								<a href="transactions-list.html"><i class="fe fe-activity"></i> <span>Transactions</span></a>
							</li>
							<li> 
								<a href="settings.html"><i class="fe fe-vector"></i> <span>Settings</span></a>
							</li>
							<li class="submenu">
								<a href="#"><i class="fe fe-document"></i> <span> Reports</span> <span class="menu-arrow"></span></a>
								<ul style="display: none;">
									<li><a href="invoice-report.html">Invoice Reports</a></li>
								</ul>
							</li>
							<li class="menu-title"> 
								<span>Pages</span>
							</li>
							<li> 
								<a href="profile.html"><i class="fe fe-user-plus"></i> <span>Profile</span></a>
							</li>
							<li class="submenu">
								<a href="#"><i class="fe fe-document"></i> <span> Authentication </span> <span class="menu-arrow"></span></a>
								<ul style="display: none;">
									<li><a href="login.html"> Login </a></li>
									<li><a href="register.html"> Register </a></li>
									<li><a href="forgot-password.html"> Forgot Password </a></li>
									<li><a href="lock-screen.html"> Lock Screen </a></li>
								</ul>
							</li>
							<li class="submenu">
								<a href="#"><i class="fe fe-warning"></i> <span> Error Pages </span> <span class="menu-arrow"></span></a>
								<ul style="display: none;">
									<li><a href="error-404.html">404 Error </a></li>
									<li><a href="error-500.html">500 Error </a></li>
								</ul>
							</li>
							<li> 
								<a href="blank-page.html"><i class="fe fe-file"></i> <span>Blank Page</span></a>
							</li>
							<li class="menu-title"> 
								<span>UI Interface</span>
							</li>
							<li> 
								<a href="components.html"><i class="fe fe-vector"></i> <span>Components</span></a>
							</li>
							<li class="submenu">
								<a href="#"><i class="fe fe-layout"></i> <span> Forms </span> <span class="menu-arrow"></span></a>
								<ul style="display: none;">
									<li><a href="form-basic-inputs.html">Basic Inputs </a></li>
									<li><a href="form-input-groups.html">Input Groups </a></li>
									<li><a href="form-horizontal.html">Horizontal Form </a></li>
									<li><a href="form-vertical.html"> Vertical Form </a></li>
									<li><a href="form-mask.html"> Form Mask </a></li>
									<li><a href="form-validation.html"> Form Validation </a></li>
								</ul>
							</li>
							<li class="submenu">
								<a href="#"><i class="fe fe-table"></i> <span> Tables </span> <span class="menu-arrow"></span></a>
								<ul style="display: none;">
									<li><a href="tables-basic.html">Basic Tables </a></li>
									<li><a href="data-tables.html">Data Table </a></li>
								</ul>
							</li>
							<li class="submenu">
								<a href="javascript:void(0);"><i class="fe fe-code"></i> <span>Multi Level</span> <span class="menu-arrow"></span></a>
								<ul style="display: none;">
									<li class="submenu">
										<a href="javascript:void(0);"> <span>Level 1</span> <span class="menu-arrow"></span></a>
										<ul style="display: none;">
											<li><a href="javascript:void(0);"><span>Level 2</span></a></li>
											<li class="submenu">
												<a href="javascript:void(0);"> <span> Level 2</span> <span class="menu-arrow"></span></a>
												<ul style="display: none;">
													<li><a href="javascript:void(0);">Level 3</a></li>
													<li><a href="javascript:void(0);">Level 3</a></li>
												</ul>
											</li>
											<li><a href="javascript:void(0);"> <span>Level 2</span></a></li>
										</ul>
									</li>
									<li>
										<a href="javascript:void(0);"> <span>Level 1</span></a>
									</li>
								</ul>
							</li> -->
						</ul>
					</div>
                </div>
            </div>
			<!-- /Sidebar -->