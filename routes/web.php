<?php

use Illuminate\Support\Facades\Route;

//New Route
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\admin\{UserListController, TeacherListController, StudentListController, ClassListController ,
                                SubjectListController ,StudentMarkListController, StudentProgressMarkingController,
                                ClassComparisonController ,ProgressChartController ,StudentReadmissionController, 
                                DesignationController, StudentProgressAddController, ReportController};

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
            Route::get('/', [UserListController::class, 'index'])->name('admin.employee.index')->middleware('check.permission');
            Route::get('/create', [UserListController::class, 'create'])->name('admin.employee.create')->middleware('check.permission');
            Route::post('/store', [UserListController::class, 'store'])->name('admin.employee.store');
            Route::get('/show/{id}', [UserListController::class, 'show'])->name('admin.employee.show')->middleware('check.permission');
            Route::get('/edit/{id}', [UserListController::class, 'edit'])->name('admin.employee.edit')->middleware('check.permission');
            Route::post('/update', [UserListController::class, 'update'])->name('admin.employee.update');
            Route::get('/status/{id}', [UserListController::class, 'status'])->name('admin.employee.status');
            Route::post('/delete', [UserListController::class, 'delete'])->name('admin.employee.delete')->middleware('check.permission');
            Route::get('/export', [UserListController::class, 'export'])->name('admin.employee.export')->middleware('check.permission');
        });

        //teachermanagement/teacherlist
        Route::prefix('teachers/')->group(function() {
            Route::get('/', [TeacherListController::class, 'index'])->name('admin.teacher.index')->middleware('check.permission');
            Route::get('/create', [TeacherListController::class, 'create'])->name('admin.teacher.create')->middleware('check.permission');
            Route::post('/store', [TeacherListController::class, 'store'])->name('admin.teacher.store');
            Route::get('/show/{id}', [TeacherListController::class, 'show'])->name('admin.teacher.show')->middleware('check.permission');
            Route::get('/edit/{id}', [TeacherListController::class, 'edit'])->name('admin.teacher.edit')->middleware('check.permission');
            Route::post('/update', [TeacherListController::class, 'update'])->name('admin.teacher.update');
            Route::get('/status/{id}', [TeacherListController::class, 'status'])->name('admin.teacher.status');
            Route::post('/delete', [TeacherListController::class, 'delete'])->name('admin.teacher.delete')->middleware('check.permission');
            Route::get('/export', [TeacherListController::class, 'export'])->name('admin.teacher.export')->middleware('check.permission');

            //get class and subject
            Route::post('/get-subject-by-class', [TeacherListController::class, 'getSubjectsByClass'])->name('admin.getSubjectsByClass');
        });

        Route::prefix('student-management')->group(function(){
            Route::prefix('student-list')->group(function(){
                Route::get('/', [StudentListController::class, 'index'])->name('admin.studentlist')->middleware('check.permission');
                Route::get('/create', [StudentListController::class, 'create'])->name('admin.studentcreate')->middleware('check.permission');
                Route::post('/store', [StudentListController::class, 'store'])->name('admin.studentstore');
                Route::get('/show/{id}', [StudentListController::class, 'show'])->name('admin.student.show')->middleware('check.permission');
                Route::get('/edit/{id}', [StudentListController::class, 'edit'])->name('admin.studentedit')->middleware('check.permission');
                Route::post('/update/{id}', [StudentListController::class, 'update'])->name('admin.studentupdate');
                Route::get('/status/{id}', [StudentListController::class, 'status'])->name('admin.studentstatus');
                Route::get('/get-sections', [StudentListController::class, 'getSections'])->name('admin.student.get-sections');
                Route::post('/delete', [StudentListController::class, 'delete'])->name('admin.studentdelete')->middleware('check.permission');
                Route::get('/export', [StudentListController::class, 'export'])->name('admin.student.export')->middleware('check.permission');
                Route::post('/import', [StudentListController::class, 'import'])->name('admin.student.import');

            });

            // Route::prefix('admission-history')->group(function(){
            //     Route::get('/{id}', [StudentListController::class, 'admissionHistory'])->name('admin.student.admissionhistory');
            //     Route::post('/update', [StudentListController::class, 'admissionhistoryUpdate'])->name('admin.student.admissionhistoryUpdate');
            //     Route::get('/re-admission/{id}', [StudentListController::class, 'reAdmissionForm'])->name('admin.student.readmission');
            //     Route::post('/re-admission/store/{id}', [StudentListController::class, 'reAdmissionStore'])->name('admin.student.readmission.store');
            // });


            Route::prefix('readmission')->group(function () {
                Route::get('/', [StudentReadmissionController::class, 'index'])->name('admin.student.readmission.index')->middleware('check.permission');
                Route::get('/history', [StudentReadmissionController::class, 'admissionHistory'])->name('admin.student.admissionhistory');
                Route::post('/update', [StudentReadmissionController::class, 'admissionhistoryUpdate'])->name('admin.student.admissionhistoryUpdate');
                Route::get('/form/{id}', [StudentReadmissionController::class, 'reAdmissionForm'])->name('admin.student.readmission');
                Route::post('/store/{id}', [StudentReadmissionController::class, 'reAdmissionStore'])->name('admin.student.readmission.store')->middleware('check.permission');
            });


            Route::prefix('student-progress-marking')->group(function(){
                Route::get('/', [StudentProgressAddController::class, 'selectionPage'])->name('admin.student.progressmarking.select');
                Route::get('/go', [StudentProgressAddController::class, 'goToMarking'])->name('admin.student.progressmarkinglist.redirect');
                Route::get('/get-classes-by-session', [StudentProgressAddController::class, 'getClassesBySession'])
                    ->name('admin.getClassesBySession');
                Route::get('/get-students-by-class', [StudentProgressAddController::class, 'getStudentsByClass'])
                    ->name('admin.getStudentsByClass');
                Route::get('/{student_id}/{session}', [StudentProgressAddController::class, 'studentProgressList'])->name('admin.student.progressmarkinglist')->middleware('check.permission');
                Route::post('/progress-update-phase', [StudentProgressAddController::class, 'ProgressUpdatePhase'])->name('admin.student.progress.update.phase');
                // Route::get('/student-progress-marking', [StudentProgressAddController::class, 'selectStudentSession'])->name('admin.student.progressmarking.select');
                // Route::get('/student-progress-marking/go', [StudentProgressAddController::class, 'redirectToMarking'])->name('admin.student.progressmarkinglist.redirect');

            });

            Route::prefix('studentmark-list')->group(function(){
                Route::get('/', [StudentMarkListController::class, 'index'])->name('admin.studentmarklist')->middleware('check.permission');
                Route::get('/get-students-by-session', [StudentMarkListController::class, 'getStudentsBySession'])->name('admin.get-students-by-session');
                Route::get('/get-class-by-session-and-student', [StudentMarkListController::class, 'getClassBySessionAndStudent'])->name('admin.get-class-by-session-and-student');
                Route::get('/student-marks/edit-data/{id}', [StudentMarkListController::class, 'getEditData'])->name('admin.student-marks.getData');
                Route::post('/store', [StudentMarkListController::class, 'storeStudentMarks'])->name('admin.student-marks.store')->middleware('check.permission');
                Route::post('/update', [StudentMarkListController::class, 'update'])->name('admin.student-marks.update')->middleware('check.permission');
                Route::post('/delete', [StudentMarkListController::class, 'delete'])->name('admin.student-marks.delete')->middleware('check.permission');
                Route::get('/export', [StudentMarkListController::class, 'export'])->name('admin.student-marks.export')->middleware('check.permission');
            });


            Route::prefix('class-wise-comparison')->group(function() {
                Route::get('/{student_id}', [ClassComparisonController::class, 'index'])->name('admin.student.classcompare')->middleware('check.permission');
                Route::post('/get-class-by-session', [ClassComparisonController::class, 'getClassBySession'])->name('admin.student.getClass');
                Route::post('/get-subjects-by-class', [ClassComparisonController::class, 'getSubjectsByClass'])->name('admin.student.getSubjects');
                Route::post('/compare-marks', [ClassComparisonController::class, 'compareMarks'])->name('admin.student.comparemarks');
            });
            
        });


        //Master module
        Route::prefix('master-module')->group(function(){
            Route::prefix('class-list')->group(function(){
                Route::get('/', [ClassListController::class, 'index'])->name('admin.classlist')->middleware('check.permission');
                Route::get('/create', [ClassListController::class, 'create'])->name('admin.classcreate')->middleware('check.permission');
                Route::post('/store', [ClassListController::class, 'store'])->name('admin.classstore');
                Route::get('/edit/{id}', [ClassListController::class, 'edit'])->name('admin.classedit')->middleware('check.permission');
                Route::post('/update/{id}', [ClassListController::class, 'update'])->name('admin.classupdate');
                Route::get('/status/{id}', [ClassListController::class, 'status'])->name('admin.classstatus');
                Route::post('/delete', [ClassListController::class, 'delete'])->name('admin.classdelete')->middleware('check.permission');

                //classwise subject
                Route::get('/subjects/{id}', [ClassListController::class, 'subjectsList'])->name('admin.class.subjects')->middleware('check.permission');
                Route::post('/subjects/add-subject', [ClassListController::class, 'addSubjectToclass'])->name('admin.class.subjects.assign')->middleware('check.permission');
                Route::post('/subjects/delete', [ClassListController::class, 'deleteSubjectToclass'])->name('admin.class.subjects.delete')->middleware('check.permission');
            });
        
            Route::prefix('subject-list')->group(function(){
                Route::get('/', [SubjectListController::class, 'index'])->name('admin.subjectlist.index')->middleware('check.permission');               
                Route::get('/create', [SubjectListController::class, 'create'])->name('admin.subjectlist.create')->middleware('check.permission');
                Route::post('/store', [SubjectListController::class, 'store'])->name('admin.subjectlist.store');
                Route::post('/update', [SubjectListController::class, 'update'])->name('admin.subjectlist.update')->middleware('check.permission');
                Route::get('/status/{id}', [SubjectListController::class, 'status'])->name('admin.subjectlist.status');
                Route::post('/delete', [SubjectListController::class, 'delete'])->name('admin.subjectlist.delete')->middleware('check.permission');
            });

            Route::prefix('progress-marking-categories')->group(function(){
                Route::get('/', [StudentProgressMarkingController::class, 'studentProgress'])->name('admin.student.progresslist')->middleware('check.permission');
                Route::post('/store', [StudentProgressMarkingController::class, 'studentProgressStore'])->name('admin.student.progressstore')->middleware('check.permission');
                Route::post('/update/{id}', [StudentProgressMarkingController::class, 'studentProgressUpdate'])->name('admin.student.progressupdate')->middleware('check.permission');
                Route::get('/status/{id}', [StudentProgressMarkingController::class, 'studentProgressStatusToggle'])->name('admin.student.progressstatus');
                Route::post('/delete', [StudentProgressMarkingController::class, 'studentProgressDelete'])->name('admin.student.progressdelete')->middleware('check.permission');
               
            });



            Route::prefix('designations')->group(function(){
                Route::get('/',[DesignationController::class, 'index'])->name('admin.designation.list')->middleware('check.permission');
                Route::post('/store', [DesignationController::class, 'store'])->name('admin.designation.store');
                Route::post('/update', [DesignationController::class, 'update'])->name('admin.designation.update');
                Route::get('/status/{id}', [DesignationController::class, 'status'])->name('admin.designation.status');
                Route::get('/permissions/{id}', [DesignationController::class, 'permissions'])->name('admin.designation.permissions');
                Route::post('/update-permissions', [DesignationController::class, 'updatePermissions'])->name('admin.designation.permissions.update');
                Route::post('/permission-ajax', [DesignationController::class, 'updatePermissionAjax'])->name('admin.designation.permissions.ajax');
            });
        });


        Route::prefix('progress-chart')->group(function(){
            Route::get('/',[ProgressChartController::class, 'index'])->name('admin.progresschart')->middleware('check.permission');
            Route::get('get-students-by-session', [ProgressChartController::class, 'getStudentsBySession'])->name('admin.getStudentsBySession');
            Route::get('get-class-subject-by-student', [ProgressChartController::class, 'getClassBySessionAndStudent'])->name('admin.getClassBySessionAndStudent');
            Route::get('/data',[ProgressChartController::class, 'fetchChartData'])->name('admin.fetchchartdata');
        });

        Route::prefix('report')->group(function(){
            Route::get('/',[ReportController::class, 'index'])->name('admin.report.index');
            Route::get('/chart-data', [ReportController::class, 'getChartData'])->name('admin.report.getChartData');
            Route::get('/academic-reports/classes-by-session', [ReportController::class, 'getClassesBySession'])->name('admin.report.getClassesBySession');
            Route::get('/academic-reports/subjects-by-class', [ReportController::class, 'getSubjectsByClassAndSession'])->name('admin.report.getSubjectsByClassAndSession');
            Route::get('/academic-reports/students-by-class-session', [ReportController::class, 'getStudentsByClassAndSession'])->name('admin.report.getStudentsByClassAndSession');
            Route::get('/student-report-card', [ReportController::class, 'getStudentReportCard'])->name('admin.report.getStudentReportCard');
            Route::get('/export', [ReportController::class, 'export'])->name('admin.report.export');
        });

    });
});