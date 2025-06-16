<?php

use Illuminate\Support\Facades\Route;

//New Route
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\{UserListController, TeacherListController, StudentListController, ClassListController ,
                                SubjectListController ,StudentMarkListController};

//End New Route

use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\cards\CardBasic;
use App\Http\Controllers\user_interface\Accordion;
use App\Http\Controllers\user_interface\Alerts;
use App\Http\Controllers\user_interface\Badges;
use App\Http\Controllers\user_interface\Buttons;
use App\Http\Controllers\user_interface\Carousel;
use App\Http\Controllers\user_interface\Collapse;
use App\Http\Controllers\user_interface\Dropdowns;
use App\Http\Controllers\user_interface\Footer;
use App\Http\Controllers\user_interface\ListGroups;
use App\Http\Controllers\user_interface\Modals;
use App\Http\Controllers\user_interface\Navbar;
use App\Http\Controllers\user_interface\Offcanvas;
use App\Http\Controllers\user_interface\PaginationBreadcrumbs;
use App\Http\Controllers\user_interface\Progress;
use App\Http\Controllers\user_interface\Spinners;
use App\Http\Controllers\user_interface\TabsPills;
use App\Http\Controllers\user_interface\Toasts;
use App\Http\Controllers\user_interface\TooltipsPopovers;
use App\Http\Controllers\user_interface\Typography;
use App\Http\Controllers\extended_ui\PerfectScrollbar;
use App\Http\Controllers\extended_ui\TextDivider;
use App\Http\Controllers\icons\RiIcons;
use App\Http\Controllers\form_elements\BasicInput;
use App\Http\Controllers\form_elements\InputGroups;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\tables\Basic as TablesBasic;

// Main Page Route
//Route::get('/', [Analytics::class, 'index'])->name('dashboard-analytics');
Route::get('/', function () {
    return redirect()->route('auth-login-basic');
});

// layout
Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');

// pages
Route::get('/pages/account-settings-account', [AccountSettingsAccount::class, 'index'])->name('pages-account-settings-account');
Route::get('/pages/account-settings-notifications', [AccountSettingsNotifications::class, 'index'])->name('pages-account-settings-notifications');
Route::get('/pages/account-settings-connections', [AccountSettingsConnections::class, 'index'])->name('pages-account-settings-connections');
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
Route::get('/pages/misc-under-maintenance', [MiscUnderMaintenance::class, 'index'])->name('pages-misc-under-maintenance');

// authentication
// Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
// Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
// Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');

// cards
Route::get('/cards/basic', [CardBasic::class, 'index'])->name('cards-basic');

// User Interface
Route::get('/ui/accordion', [Accordion::class, 'index'])->name('ui-accordion');
Route::get('/ui/alerts', [Alerts::class, 'index'])->name('ui-alerts');
Route::get('/ui/badges', [Badges::class, 'index'])->name('ui-badges');
Route::get('/ui/buttons', [Buttons::class, 'index'])->name('ui-buttons');
Route::get('/ui/carousel', [Carousel::class, 'index'])->name('ui-carousel');
Route::get('/ui/collapse', [Collapse::class, 'index'])->name('ui-collapse');
Route::get('/ui/dropdowns', [Dropdowns::class, 'index'])->name('ui-dropdowns');
Route::get('/ui/footer', [Footer::class, 'index'])->name('ui-footer');
Route::get('/ui/list-groups', [ListGroups::class, 'index'])->name('ui-list-groups');
Route::get('/ui/modals', [Modals::class, 'index'])->name('ui-modals');
Route::get('/ui/navbar', [Navbar::class, 'index'])->name('ui-navbar');
Route::get('/ui/offcanvas', [Offcanvas::class, 'index'])->name('ui-offcanvas');
Route::get('/ui/pagination-breadcrumbs', [PaginationBreadcrumbs::class, 'index'])->name('ui-pagination-breadcrumbs');
Route::get('/ui/progress', [Progress::class, 'index'])->name('ui-progress');
Route::get('/ui/spinners', [Spinners::class, 'index'])->name('ui-spinners');
Route::get('/ui/tabs-pills', [TabsPills::class, 'index'])->name('ui-tabs-pills');
Route::get('/ui/toasts', [Toasts::class, 'index'])->name('ui-toasts');
Route::get('/ui/tooltips-popovers', [TooltipsPopovers::class, 'index'])->name('ui-tooltips-popovers');
Route::get('/ui/typography', [Typography::class, 'index'])->name('ui-typography');

// extended ui
Route::get('/extended/ui-perfect-scrollbar', [PerfectScrollbar::class, 'index'])->name('extended-ui-perfect-scrollbar');
Route::get('/extended/ui-text-divider', [TextDivider::class, 'index'])->name('extended-ui-text-divider');

// icons
Route::get('/icons/icons-ri', [RiIcons::class, 'index'])->name('icons-ri');

// form elements
Route::get('/forms/basic-inputs', [BasicInput::class, 'index'])->name('forms-basic-inputs');
Route::get('/forms/input-groups', [InputGroups::class, 'index'])->name('forms-input-groups');

// form layouts
Route::get('/form/layouts-vertical', [VerticalForm::class, 'index'])->name('form-layouts-vertical');
Route::get('/form/layouts-horizontal', [HorizontalForm::class, 'index'])->name('form-layouts-horizontal');

// tables
Route::get('/tables/basic', [TablesBasic::class, 'index'])->name('tables-basic');

//admin
Route::prefix('admin')->group(function () {
    //authentication
    // Show pages
    Route::get('/login', [LoginBasic::class, 'index'])->name('auth-login-basic');
    Route::get('/register', [RegisterBasic::class, 'index'])->name('auth-register-basic');
    Route::get('/forgot-password', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');

    // Form submission
    Route::post('/register', [AdminAuthController::class, 'register'])->name('admin.register.submit');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    //for reset password
    Route::post('/forgot-password', [AdminAuthController::class, 'resetPassword'])->name('admin.reset-password');

    // Protected admin dashboard
    Route::middleware('admin', 'prevent-back-history')->group(function () {
        Route::get('/dashboard', [Analytics::class, 'index'])->name('admin.dashboard');
        Route::get('/profile', [AdminAuthController::class, 'profile'])->name('admin.profile');
        Route::post('/update-profile', [AdminAuthController::class, 'updateProfile'])->name('admin.profile.update');

        //usermanagement/userlist
        Route::prefix('employees/')->group(function() {
            Route::get('/', [UserListController::class, 'index'])->name('admin.employee.index');
            Route::get('/create', [UserListController::class, 'create'])->name('admin.employee.create');
            Route::post('/store', [UserListController::class, 'store'])->name('admin.employee.store');
            Route::get('/edit/{id}', [UserListController::class, 'edit'])->name('admin.employee.edit');
            Route::post('/update', [UserListController::class, 'update'])->name('admin.employee.update');
            Route::get('/status/{id}', [UserListController::class, 'status'])->name('admin.employee.status');
            Route::post('/delete', [UserListController::class, 'delete'])->name('admin.employee.delete');
        });

        //teachermanagement/teacherlist
        Route::prefix('teachers/')->group(function() {
            Route::get('/', [TeacherListController::class, 'index'])->name('admin.teacher.index');
            Route::get('/create', [TeacherListController::class, 'create'])->name('admin.teacher.create');
            Route::post('/store', [TeacherListController::class, 'store'])->name('admin.teacher.store');
            Route::get('/show/{id}', [TeacherListController::class, 'show'])->name('admin.teacher.show');
            Route::get('/edit/{id}', [TeacherListController::class, 'edit'])->name('admin.teacher.edit');
            Route::post('/update', [TeacherListController::class, 'update'])->name('admin.teacher.update');
            Route::get('/status/{id}', [TeacherListController::class, 'status'])->name('admin.teacher.status');
            Route::post('/delete', [TeacherListController::class, 'delete'])->name('admin.teacher.delete');

            //get class and subject
            Route::post('/get-subject-by-class', [TeacherListController::class, 'getSubjectsByClass'])->name('admin.getSubjectsByClass');
        });

        Route::prefix('student-management')->group(function(){
            Route::prefix('student-list')->group(function(){
                Route::get('/', [StudentListController::class, 'index'])->name('admin.studentlist');
                Route::get('/create', [StudentListController::class, 'create'])->name('admin.studentcreate');
                Route::post('/store', [StudentListController::class, 'store'])->name('admin.studentstore');
                Route::get('/edit/{id}', [StudentListController::class, 'edit'])->name('admin.studentedit');
                Route::post('/update/{id}', [StudentListController::class, 'update'])->name('admin.studentupdate');
                Route::get('/status/{id}', [StudentListController::class, 'status'])->name('admin.studentstatus');
                Route::get('/get-sections', [StudentListController::class, 'getSections'])->name('admin.student.get-sections');
                Route::post('/delete', [StudentListController::class, 'delete'])->name('admin.studentdelete');
                Route::get('/admission-history/{id}', [StudentListController::class, 'admissionHistory'])->name('admin.student.admissionhistory');
                Route::post('/admission-history/update', [StudentListController::class, 'admissionhistoryUpdate'])->name('admin.student.admissionhistoryUpdate');
                Route::get('/re-admission/{id}', [StudentListController::class, 'reAdmissionForm'])->name('admin.student.readmission');
                Route::post('/re-admission/store/{id}', [StudentListController::class, 'reAdmissionStore'])->name('admin.student.readmission.store');
                Route::get('/export', [StudentListController::class, 'export'])->name('admin.student.export');
            });

            Route::prefix('studentmark-list')->group(function(){
                Route::get('/', [StudentMarkListController::class, 'index'])->name('admin.studentmarklist');
            });
            
        });


        //Master module
        Route::prefix('master-module')->group(function(){
            Route::prefix('class-list')->group(function(){
                Route::get('/', [ClassListController::class, 'index'])->name('admin.classlist');
                Route::get('/create', [ClassListController::class, 'create'])->name('admin.classcreate');
                Route::post('/store', [ClassListController::class, 'store'])->name('admin.classstore');
                Route::get('/edit/{id}', [ClassListController::class, 'edit'])->name('admin.classedit');
                Route::post('/update/{id}', [ClassListController::class, 'update'])->name('admin.classupdate');
                Route::get('/status/{id}', [ClassListController::class, 'status'])->name('admin.classstatus');
                Route::post('/delete', [ClassListController::class, 'delete'])->name('admin.classdelete');

                //classwise subject
                Route::get('/subjects/{id}', [ClassListController::class, 'subjectsList'])->name('admin.class.subjects');
                Route::post('/subjects/add-subject', [ClassListController::class, 'addSubjectToclass'])->name('admin.class.subjects.assign');
                Route::post('/subjects/delete', [ClassListController::class, 'deleteSubjectToclass'])->name('admin.class.subjects.delete');
            });
        
            Route::prefix('subject-list')->group(function(){
                Route::get('/', [SubjectListController::class, 'index'])->name('admin.subjectlist.index');               
                // Route::get('/edit/{id}', [SubjectListController::class, 'edit'])->name('admin.subjectlist.edit');
                Route::get('/create', [SubjectListController::class, 'create'])->name('admin.subjectlist.create');
                Route::post('/store', [SubjectListController::class, 'store'])->name('admin.subjectlist.store');
                Route::post('/update', [SubjectListController::class, 'update'])->name('admin.subjectlist.update');
                Route::get('/status/{id}', [SubjectListController::class, 'status'])->name('admin.subjectlist.status');
                Route::post('/delete', [SubjectListController::class, 'delete'])->name('admin.subjectlist.delete');
            });
        });

    });
});