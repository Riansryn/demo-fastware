<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FormFPPController;
use App\Http\Controllers\HandlingController;
use App\Http\Controllers\HeatTreatmentController;
use App\Http\Controllers\InquirySalesController;
use App\Http\Controllers\KmPengajuanController;
use App\Http\Controllers\MesinController;
use App\Http\Controllers\PenilaianTCController;
use App\Http\Controllers\PdController;
use App\Http\Controllers\PreventiveController;
use App\Http\Controllers\SafetyController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\SumbangSaranController;
use App\Http\Controllers\TcController;
use App\Http\Controllers\TcJobController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PoPengajuanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Proses login
Route::resource('mesins', MesinController::class);
Route::resource('users', UserController::class);
Route::resource('customers', CustomerController::class);
Route::resource('formperbaikans', FormFPPController::class);
Route::resource('receivedfpps', FormFPPController::class);
Route::resource('approvedfpps', FormFPPController::class);
Route::resource('tindaklanjuts', FormFPPController::class);
Route::resource('preventives', PreventiveController::class);
Route::resource('spareparts', SparepartController::class);

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login_post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('full-calender', [EventController::class, 'blokMaintanence'])->name('blokMaintanence');
    Route::get('full-calenderDept', [EventController::class, 'blokDeptMaintenance'])->name('blokDeptMaintenance');

    Route::post('full-calender-AJAX', [EventController::class, 'ajax']);
    Route::get('generate-pdf/{mesin}', 'App\Http\Controllers\PDFController@generatePDF')->name('pdf.mesin');
    Route::get('dashboardMaintenance', [EventController::class, 'dashboardMaintenance'])->name('dashboardMaintenance');

    // Change Pass
    Route::get('/showDataDiri', 'App\Http\Controllers\AuthController@showDataDiri')->name('showDataDiri');
    Route::post('/ubahPassword', 'App\Http\Controllers\AuthController@ubahPassword')->name('ubahPassword');
    Route::post('/ubahDataDiri', 'App\Http\Controllers\AuthController@ubahDataDiri')->name('ubahDataDiri');

    // Admin
    Route::get('dashboardusers', [UserController::class, 'index'])->name('dashboardusers');
    Route::get('dashboardcustomers', [CustomerController::class, 'index'])->name('dashboardcustomers');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

    // Preventive
    Route::get('dashpreventive', [PreventiveController::class, 'maintenanceDashPreventive'])
        ->name('maintenance.dashpreventive');
    Route::get('deptmtcepreventive', [PreventiveController::class, 'deptmtceDashPreventive'])
        ->name('deptmtce.dashpreventive');
    Route::get('deptmtce/editpreventive/{mesin}', [PreventiveController::class, 'EditDeptMTCEPreventive'])
        ->name('deptmtce.editpreventive');

    // Production
    Route::get('dashboardproduction', [FormFPPController::class, 'DashboardProduction'])->name('fpps.index');
    Route::post('store', [FormFPPController::class, 'store'])->name('formperbaikans.store');
    Route::get('historyfpp', [FormFPPController::class, 'HistoryFPP'])->name('fpps.history');
    Route::get('lihatform/{formperbaikan}', [FormFPPController::class, 'LihatFPP'])
        ->name('fpps.show');
    Route::get('closedform/{formperbaikan}', [FormFPPController::class, 'ClosedFormProduction'])
        ->name('fpps.closed');

    // Maintenance
    Route::get('dashboardmaintenance', [FormFPPController::class, 'DashboardMaintenance'])
        ->name('maintenance.index');
    Route::get('dashboardmaintenancega', [FormFPPController::class, 'DashboardMaintenanceGA'])
        ->name('ga.dashboardga');
    Route::get('lihatmaintenance/{formperbaikan}', [FormFPPController::class, 'LihatMaintenance'])
        ->name('maintenance.lihat');
    Route::get('editmaintenance/{formperbaikan}', [FormFPPController::class, 'EditMaintenance'])
        ->name('maintenance.edit');
    Route::get('preventives/edit-issue/{preventive}', [PreventiveController::class, 'editIssue'])
        ->name('preventives.editpreventive');
    Route::get('preventives/lihat-issue/{preventive}', [PreventiveController::class, 'lihatIssue'])
        ->name('preventives.lihatpreventive');
    Route::put('preventives/update-issue/{preventive}', [PreventiveController::class, 'updateIssue'])
        ->name('preventives.updateIssue');

    Route::get('dashboardmesins', [MesinController::class, 'index'])->name('dashboardmesins');
    Route::get('dashboardgamesin', [MesinController::class, 'dashboardGAMesin'])->name('dashboardgamesin');
    Route::get('/mesins/showMesinGA/{mesin}', [MesinController::class, 'showMesinGA'])->name('mesins.showMesinGA');

    // Dept Maintenance
    Route::get('dashboarddeptmtce', [FormFPPController::class, 'DashboardDeptMTCE'])
        ->name('deptmtce.index');
    Route::get('dashboardapprovedga', [FormFPPController::class, 'DashboardFPPGA'])
        ->name('ga.approvedfpp');
    Route::get('lihatdeptmtce/{formperbaikan}', [FormFPPController::class, 'LihatDeptMTCE'])
        ->name('deptmtce.show');
    Route::get('editdeptmtcepreventive/{mesin}', [PreventiveController::class, 'EditDeptMTCEPreventive'])
        ->name('deptmtce.lihatpreventive');
    Route::get('dashboardPreventive', [PreventiveController::class, 'dashboardPreventive'])->name('dashboardPreventive');
    Route::get('dashboardPreventiveMaintenance', [PreventiveController::class, 'dashboardPreventiveMaintenance'])->name('dashboardPreventiveMaintenance');
    Route::get('dashboardPreventiveMaintenanceGA', [PreventiveController::class, 'dashboardPreventiveMaintenanceGA'])->name('dashboardPreventiveMaintenanceGA');
    Route::get('formpreventif', [PreventiveController::class, 'create'])->name('preventives.create');
    Route::get('editpreventive', [PreventiveController::class, 'edit'])->name('preventives.edit');
    Route::post('sparepart-import', [SparepartController::class, 'import'])->name('spareparts.import');
    Route::get('/spareparts/export/{nomor_mesin}', [SparepartController::class, 'export'])->name('spareparts.export');

    Route::put('/update-preventive', [PreventiveController::class, 'update'])->name('updatePreventive');

    // Sales
    Route::get('dashboardfppsales', [FormFPPController::class, 'DashboardFPPSales'])
        ->name('sales.index');
    Route::get('historysales', [FormFPPController::class, 'HistorySales'])
        ->name('sales.history');
    Route::get('lihatfppsales/{formperbaikan}', [FormFPPController::class, 'LihatFPPSales'])
        ->name('sales.lihat');

    // Download File
    Route::get('download-excel/{tindaklanjut}', [FormFPPController::class, 'downloadAttachment'])->name('download.attachment');
    // DashboardforALL
    Route::get('/dashboardHandling', 'App\Http\Controllers\DsController@dashboardHandling')->name('dashboardHandling');
    Route::get('/dashboardMaintenance', 'App\Http\Controllers\DsController@dashboardMaintenance')->name('dashboardMaintenance');
    Route::get('/dshandling', 'App\Http\Controllers\DsController@dshandling')->name('dshandling');
    Route::get('/getChartData', 'App\Http\Controllers\HandlingController@getChartData')->name('getChartData');
    Route::get('/get-data-by-year', 'App\Http\Controllers\HandlingController@getDataByYear')->name('getDataByYear');
    Route::get('/api/filter-pie-chart-tipe', 'App\Http\Controllers\HandlingController@FilterPieChartTipe')->name('FilterPieChartTipe');
    Route::get('/api/filter-tipe-all', 'App\Http\Controllers\HandlingController@FilterTipeAll');
    Route::get('/api/FilterPieChartProses', 'App\Http\Controllers\HandlingController@FilterPieChartProses')->name('FilterPieChartProses');
    Route::get('/api/filterPieChartNG', [HandlingController::class, 'filterPieChartNG'])->name('filterPieChartNG');
    Route::get('/api/getChartStatusHandling', 'App\Http\Controllers\HandlingController@getChartStatusHandling')->name('getChartStatusHandling');
    Route::get('/export-handlings', 'App\Http\Controllers\HandlingController@export')->name('export.handlings');

    // Grafik Repair Maintenance
    // Route::get('/getRepairMaintenance', 'App\Http\Controllers\MaintenanceController@getRepairMaintenance')->name('getRepairMaintenance');
    Route::get('/getMaintenanceData', 'App\Http\Controllers\MaintenanceController@getMaintenanceData')->name('getMaintenanceData');
    Route::get('/getMaintenanceDataAlat', 'App\Http\Controllers\MaintenanceController@getMaintenanceDataAlat')->name('getMaintenanceDataAlat');
    Route::get('/getRepairAlatBantu', 'App\Http\Controllers\MaintenanceController@getRepairAlatBantu')->name('getRepairAlatBantu');
    // Route::get('/getPeriodeWaktuPengerjaan', 'App\Http\Controllers\MaintenanceController@getPeriodeWaktuPengerjaan')->name('getPeriodeWaktuPengerjaan');
    Route::get('/getPeriodeWaktuAlat', 'App\Http\Controllers\MaintenanceController@getPeriodeWaktuAlat')->name('getPeriodeWaktuAlat');
    Route::get('/getPeriodeMesin', 'App\Http\Controllers\MaintenanceController@getPeriodeMesin')->name('getPeriodeMesin');
    Route::get('/getPeriodeAlat', 'App\Http\Controllers\MaintenanceController@getPeriodeAlat')->name('getPeriodeAlat');

    Route::get('handling', [HandlingController::class, 'index'])->name('index');
    Route::get('create', [HandlingController::class, 'create'])->name('create');
    Route::post('store', [HandlingController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [HandlingController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [HandlingController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [HandlingController::class, 'delete'])->name('delete');
    Route::patch('/changeStatus/{id}', [HandlingController::class, 'changeStatus'])->name('changeStatus');
    Route::get('/showHistory/{id}', [HandlingController::class, 'showHistory'])->name('showHistory');

    // deptMan
    Route::get('/deptMan', 'App\Http\Controllers\DeptManController@submission')->name('submission');
    Route::get('/showConfirm/{id}', 'App\Http\Controllers\DeptManController@showConfirm')->name('showConfirm');
    Route::put('/updateConfirm/{id}', 'App\Http\Controllers\DeptManController@updateConfirm')->name('updateConfirm');
    Route::get('/showFollowUp/{id}', 'App\Http\Controllers\DeptManController@showFollowUp')->name('showFollowUp');
    Route::get('/showHistoryProgres/{id}', 'App\Http\Controllers\DeptManController@showHistoryProgres')->name('showHistoryProgres');
    Route::put('/updateFollowUp/{id}', 'App\Http\Controllers\DeptManController@updateFollowUp')->name('updateFollowUp');
    Route::get('scheduleVisit', 'App\Http\Controllers\DeptManController@scheduleVisit')->name('scheduleVisit');
    Route::get('showHistoryCLaimComplain', 'App\Http\Controllers\DeptManController@showHistoryCLaimComplain')->name('showHistoryCLaimComplain');
    Route::get('/showCloseProgres/{id}', 'App\Http\Controllers\DeptManController@showCloseProgres')->name('showCloseProgres');
    // Route untuk update notes berdasarkan due_date
    Route::post('/update-notes', 'App\Http\Controllers\DeptManController@updateNotes')->name('schedule.updateNotes');

    // SS
    Route::get('/showSS', 'App\Http\Controllers\SumbangSaranController@showSS')->name('showSS');
    Route::get('/dashboardSS', 'App\Http\Controllers\SumbangSaranController@dashboardSS')->name('dashboardSS');
    Route::get('/forumSS', 'App\Http\Controllers\SumbangSaranController@forumSS')->name('forumSS');

    Route::get('/chartSection', 'App\Http\Controllers\SumbangSaranController@chartSection')->name('chartSection');
    Route::post('/chartEmployee', 'App\Http\Controllers\SumbangSaranController@chartEmployee')->name('chartEmployee');
    Route::post('/chartUser', 'App\Http\Controllers\SumbangSaranController@chartUser')->name('chartUser');
    Route::post('/chartMountEmployee', 'App\Http\Controllers\SumbangSaranController@chartMountEmployee')->name('chartMountEmployee');

    Route::post('/export-konfirmasi-hrga', 'App\Http\Controllers\SumbangSaranController@exportKonfirmasiHRGA')->name('export-konfirmasi-hrga');
    Route::post('/update-status-to-bayar', 'App\Http\Controllers\SumbangSaranController@updateStatusToBayar')->name('updateStatusToBayar');

    Route::get('/showKonfirmasiForeman', 'App\Http\Controllers\SumbangSaranController@showKonfirmasiForeman')->name('showKonfirmasiForeman');
    Route::get('/showKonfirmasiDeptHead', 'App\Http\Controllers\SumbangSaranController@showKonfirmasiDeptHead')->name('showKonfirmasiDeptHead');
    Route::get('/showKonfirmasiKomite', 'App\Http\Controllers\SumbangSaranController@showKonfirmasiKomite')->name('showKonfirmasiKomite');
    Route::get('/showKonfirmasiHRGA', 'App\Http\Controllers\SumbangSaranController@showKonfirmasiHRGA')->name('showKonfirmasiHRGA');

    Route::post('/simpanSS', 'App\Http\Controllers\SumbangSaranController@simpanSS')->name('simpanSS');
    Route::post('/simpanPenilaian', 'App\Http\Controllers\SumbangSaranController@simpanPenilaian')->name('simpanPenilaian');
    Route::post('/submitnilai', 'App\Http\Controllers\SumbangSaranController@submitNilai')->name('submitnilai');
    Route::post('/submitTambahNilai', 'App\Http\Controllers\SumbangSaranController@submitTambahNilai')->name('submitTambahNilai');
    Route::post('/sumbangsaran/like/{id}', 'App\Http\Controllers\SumbangSaranController@like')->name('sumbangsaran.like');
    Route::post('/sumbangsaran/unlike/{id}', 'App\Http\Controllers\SumbangSaranController@unlike')->name('sumbangsaran.unlike');

    Route::get('/editSS/{id}', [SumbangSaranController::class, 'editSS'])->name('editSS');
    Route::post('/updateSS/{id}', [SumbangSaranController::class, 'updateSS']);

    Route::get('/getPenilaians/{id}', [SumbangSaranController::class, 'getPenilaians'])->name('getPenilaians');
    Route::get('/getNilai/{id}', [SumbangSaranController::class, 'getNilai'])->name('getNilai');
    Route::get('/getTambahNilai/{id}', [SumbangSaranController::class, 'getTambahNilai'])->name('getTambahNilai');
    Route::get('/file/download/{filename}', [SumbangSaranController::class, 'downloadFile'])->name('file.download');

    Route::delete('/delete-ss/{id}', [SumbangSaranController::class, 'deleteSS'])->name('deleteSS');
    Route::post('/kirim-ss/{id}', [SumbangSaranController::class, 'kirimSS'])->name('kirimSS');
    Route::post('/kirim-ss2/{id}', [SumbangSaranController::class, 'kirimSS2'])->name('kirimSS2');
    Route::get('/sumbangsaran/{id}', 'App\Http\Controllers\SumbangSaranController@getSumbangSaran')->name('sumbangsaran.show');
    Route::get('/secHead/{id}', 'App\Http\Controllers\SumbangSaranController@showSecHead')->name('sechead.show');

    // Safety Patrol
    Route::get('listpatrol', [SafetyController::class, 'listSafetyPatrol'])->name('listpatrol');
    Route::get('listpatrolpic', [SafetyController::class, 'listSafetyPatrolPIC'])->name('listpatrolpic');
    Route::get('reportpatrol', [SafetyController::class, 'reportPatrol'])->name('reportpatrol');
    Route::get('buatsafetypatrol', [SafetyController::class, 'buatFormSafety'])->name('patrols.buatFormSafety');
    Route::post('simpanPatrol', [SafetyController::class, 'simpanPatrol'])->name('patrols.simpanPatrol');
    Route::get('detailPatrol/{patrol}', [SafetyController::class, 'detailPatrol'])->name('patrols.detailPatrol');
    Route::get('/get-pic-area', [SafetyController::class, 'getPICArea']);
    Route::get('/get-area-patrol', [SafetyController::class, 'getAreaPatrol']);
    Route::get('/get-kategori-patrol', [SafetyController::class, 'getKategoriPatrol']);
    Route::get('/get-safety-patrol', [SafetyController::class, 'getSafetyPatrol']);
    Route::get('/get-lingkungan-patrol', [SafetyController::class, 'getLingkunganPatrol']);
    Route::post('export-patrol-data', [SafetyController::class, 'exportData'])->name('export-patrol-data');

    // WO Heat Treatment
    Route::get('dashboardImportWO', [HeatTreatmentController::class, 'dashboardImportWO'])
        ->name('dashboardImportWO');
    Route::get('dashboardTracingWO', [HeatTreatmentController::class, 'dashboardTracingWO'])
        ->name('dashboardTracingWO');
    Route::get('landingWO', [HeatTreatmentController::class, 'landingWO'])
        ->name('landingWO');
    Route::get('/filter-wo', [HeatTreatmentController::class, 'filterWO'])->name('filter-wo');
    Route::post('importWO', [HeatTreatmentController::class, 'WOHeat'])->name('importWO');
    Route::get('/searchWO', [HeatTreatmentController::class, 'searchWO'])->name('searchWO');
    Route::get('downtimeExport', [FormFPPController::class, 'downtimeExport']);
    Route::get('/getBatchData', [HeatTreatmentController::class, 'getBatchData'])->name('getBatchData');

    // Inquiry Sales
    // view
    Route::get('createinquiry', [InquirySalesController::class, 'createInquirySales'])->name('createinquiry');
    Route::get('formulirInquiry/{id}', [InquirySalesController::class, 'formulirInquiry'])->name('formulirInquiry');
    Route::get('tindakLanjutInquiry/{id}', [InquirySalesController::class, 'tindakLanjutInquiry'])->name('tindakLanjutInquiry');
    Route::get('showFormSS/{id}', [InquirySalesController::class, 'showFormSS'])->name('showFormSS');
    Route::get('historyFormSS/{id}', [InquirySalesController::class, 'historyFormSS'])->name('historyFormSS');

    Route::get('konfirmInquiry', [InquirySalesController::class, 'konfirmInquiry'])->name('konfirmInquiry');
    Route::get('validasiInquiry', [InquirySalesController::class, 'validasiInquiry'])->name('validasiInquiry');
    Route::get('reportInquiry', [InquirySalesController::class, 'reportInquiry'])->name('reportInquiry');
    // fungsi
    Route::post('storeinquiry', [InquirySalesController::class, 'storeInquirySales'])->name('storeinquiry');
    Route::post('/inquiry/previewSS', [InquirySalesController::class, 'previewSS'])->name('inquiry.previewSS');
    Route::post('/inquiry/tindakLanjutInquiry', [InquirySalesController::class, 'saveTindakLanjut'])->name('inquiry.tindakLanjutInquiry');
    Route::put('/inquiry/{id}', [InquirySalesController::class, 'update'])->name('updateinquiry');
    Route::get('/editInquiry/{id}', [InquirySalesController::class, 'editInquiry'])->name('editInquiry');
    Route::delete('/deleteinquiry/{id}', [InquirySalesController::class, 'delete'])->name('deleteinquiry');

    Route::put('/approvedInquiry/{id}', [InquirySalesController::class, 'approvedInquiry'])->name('approvedInquiry');
    Route::put('/validateInquiry/{id}', [InquirySalesController::class, 'validateInquiry'])->name('validateInquiry');

    // km
    Route::get('/km', [KmPengajuanController::class, 'pengajuanKM'])->name('pengajuanKM');
    Route::get('/dsKnowlege', [KmPengajuanController::class, 'dsKnowlege'])->name('dsKnowlege');
    Route::get('/persetujuanKM', [KmPengajuanController::class, 'persetujuanKM'])->name('persetujuanKM');
    Route::post('/kmTransaksi/markAsRead', [KmPengajuanController::class, 'markAsRead'])->name('kmTransaksi.markAsRead');
    Route::post('/kmTransaksi/saveTransaction', [KmPengajuanController::class, 'saveTransaction'])->name('kmTransaksi.saveTransaction');

    // fungsi
    Route::post('/km', [KmPengajuanController::class, 'storeKM'])->name('storeKM');
    Route::put('/knowledge-management/update', [KmPengajuanController::class, 'update'])->name('updateKM');
    Route::get('/km/{id}/edit', [KmPengajuanController::class, 'edit'])->name('editKM');

    Route::get('/km/{id}/showPersetujuan', [KmPengajuanController::class, 'showPersetujuan'])->name('showPersetujuan');
    Route::put('/knowledge-management/approveKM', [KmPengajuanController::class, 'approveKM'])->name('approveKM');

    Route::patch('/km/{id}/update-status', [KmPengajuanController::class, 'updateStatus'])->name('updateStatusKM');
    Route::post('/kirimKM/{id}', [KmPengajuanController::class, 'kirimKM'])->name('kirimKM');
    Route::post('/like', [KmPengajuanController::class, 'like'])->name('kmSuka.like');
    Route::post('/unlike', [KmPengajuanController::class, 'unlike'])->name('kmSuka.unlike');
    Route::post('/insights/add', [KmPengajuanController::class, 'addInsight'])->name('insights.add');

    // tc
    Route::get('/job', [TcJobController::class, 'jobShow'])->name('jobShow');

    Route::get('/tcShow', [TcController::class, 'tcShow'])->name('tcShow');
    Route::get('/tcCreate', [TcController::class, 'createTC'])->name('tcCreate');
    Route::post('/mst_tc/store', [TcController::class, 'storeTC'])->name('mst_tc.store');

    Route::get('mst_tc/{id}/edit', [TcController::class, 'edit'])->name('mst_tc.edit');
    Route::get('mst_sk/{id}/editSoftSKills', [TcController::class, 'editSoftSKills'])->name('mst_sk.editSoftSKills');
    Route::get('mst_ad/{id}/editAdditionals', [TcController::class, 'editAdditional'])->name('mst_ad.editAdditionals');
    Route::get('/job-position/{id}/edit', [TcJobController::class, 'getJobPositionData'])->name('getJobPosition');

    Route::delete('/job-positions/delete-row', [TcJobController::class, 'deleteRow'])->name('jobPositions.deleteRow');

    Route::put('mst_tc/{id}', [TcController::class, 'update'])->name('mst_tc.update');
    Route::put('mst_sk/{id}/updateSoftSkills', [TcController::class, 'updateSoftSkills'])->name('mst_sk.updateSoftSkills');
    Route::put('mst_ad/{id}/updateAdditionals', [TcController::class, 'updateAdditionals'])->name('mst_ad.updateAdditionals');
    Route::get('/employees-by-job-position', [TcController::class, 'fetchEmployeesByJobPosition'])->name('employees.by.job.position');

    

    Route::get('/users/{userId}/role', [TcJobController::class, 'getUserRole'])->name('users.role');
    Route::post('/job-positions', [TcJobController::class, 'store'])->name('jobPositions.store');
    Route::put('/job-positions/{id}', [TcJobController::class, 'updateJob'])->name('jobPositions.update');
    Route::delete('/job-positions/{id}', [TcJobController::class, 'deleteRow'])->name('jobPositions.destroy');
    Route::delete('/delete-tc-row/{id}', [TcController::class, 'deleteTcRow'])->name('tc.deleteRow');
    Route::delete('/delete-sk-row/{id}', [TcController::class, 'deleteSkRow'])->name('sk.deleteRow');
    Route::delete('/delete-ad-row/{id}', [TcController::class, 'deleteAdRow'])->name('ad.deleteRow');

    // Route untuk menampilkan halaman penilaian (index)
    Route::get('/penilaian', [PenilaianTCController::class, 'indexTrs'])->name('penilaian.index');
    Route::get('/penilaian-dept', [PenilaianTCController::class, 'indexTrs2'])->name('penilaian.index2');
    Route::get('/dashboard-competency', [PenilaianTCController::class, 'dsCompetency'])->name('dsCompetency');
    Route::get('/dashboard-detail-competency', [PenilaianTCController::class, 'dsDetailCompetency'])->name('dsDetailCompetency');

    Route::get('/dashboard-people-development', [PdController::class, 'indexPD'])->name('indexPD');
    Route::get('/dashboard-people-development-hrga', [PdController::class, 'indexPD2'])->name('indexPD2');
    Route::get('/dashboard-histori-development', [PdController::class, 'historiDevelop'])->name('historiDept');


    Route::get('/buat-penilaian', [PenilaianTCController::class, 'createPenilaian'])->name('create.penilaian');
    Route::get('/buat-training', [PdController::class, 'createPD'])->name('createPD');

    Route::get('/get-job-position-data', [PenilaianTCController::class, 'getJobPositionData'])->name('getJobPositionData');
    Route::get('/get-job-position-data-edit', [PenilaianTCController::class, 'getJobPositionDataEdit'])->name('getJobPositionDataEdit');
    Route::get('/get-nilai-data-edit', [PenilaianTCController::class, 'getNilaiDataEdit'])->name('getNilaiDataEdit');
    Route::get('/get-job-point-kategori', [PenilaianTCController::class, 'getJobPointKategori'])->name('getJobPointKategori');

    Route::get('/view-pd/{modified_at}', [PdController::class, 'viewPD'])->name('viewPD');
    Route::get('/view-pd-HRGA/{id}', [PdController::class, 'viewPD2'])->name('viewPD2');

    Route::post('/save-penilaian', [PenilaianTCController::class, 'savePenilaian'])->name('savePenilaian');
    Route::post('/save-pd-pengajuan', [PdController::class, 'savePdPengajuan'])->name('savePdPengajuan');
    Route::put('/updated-pd-hrga', [PdController::class, 'updatePdPlan'])->name('updatePdPlan');

    Route::post('/update-status/{id_job_position}', [PenilaianTCController::class, 'kirimSC'])->name('update.status');
    Route::post('/update-status-dept/{id}', [PenilaianTCController::class, 'kirimDept'])->name('update.status2');

    Route::get('/editPdPengajuan/{modified_at}', [PdController::class, 'editPdPengajuan'])->name('editPdPengajuan');
    Route::get('/editPdPengajuan-HRGA/{id}', [PdController::class, 'editPdPengajuanHRGA'])->name('editPdPengajuanHRGA');

    Route::put('/update-pd', [PdController::class, 'update'])->name('updatePD');

    Route::get('/send-pd/{modified_at}', [PdController::class, 'sendPD'])->name('sendPD');
    Route::get('/send-pd2/{tahun_aktual}', [PdController::class, 'sendPD2'])->name('sendPD2');

    Route::get('/people-development/filter', [PdController::class, 'getFilteredData'])->name('people_development.filter');


    Route::get('/trs/edit-penilaian/{id_job_position}', [PenilaianTCController::class, 'editTrs'])->name('penilaian.edit');
    Route::get('/trs/edit-dept/{id_job_position}', [PenilaianTCController::class, 'editTrs2'])->name('penilaian.edit2');
    Route::get('/trs/view-penilaian/{id_job_position}', [PenilaianTCController::class, 'viewTrs'])->name('penilaian.view');
    Route::get('/trs/preview-penilaian/{id_job_position}', [PenilaianTCController::class, 'previewTrs'])->name('penilaian.preview');

    Route::get('/get-edit-Trs', [PenilaianTCController::class, 'getDataTrs'])->name('getDataTrs');
    Route::put('/penilaian/update/{id}', [PenilaianTCController::class, 'updateTrs'])->name('updatePenilaian');
    Route::put('/penilaian/dept/{id_job_position}', [PenilaianTCController::class, 'updateTrs2'])->name('updateDept');

    Route::put('/penilaian/{id}', [PenilaianTCController::class, 'update'])->name('penilaian.update');
    Route::delete('/penilaian/{id}', [PenilaianTCController::class, 'destroy'])->name('penilaian.destroy');

    Route::get('/download-pdf/{id}', [PdController::class, 'downloadPDF'])->name('download.pdf');
    // web.php
    Route::post('/update-button-status', [PdController::class, 'updateBtn'])->name('updateButtonStatus');


    //chartTC
    Route::get('/get-competency-data', [PenilaianTCController::class, 'getCompetencyData'])->name('get-competency-data');
    Route::get('/get-competency-filter', [PenilaianTCController::class, 'getCompetencyFilter'])->name('get-competency-filter');
    Route::get('/get-detail-filter', [PenilaianTCController::class, 'getDetailCompetency'])->name('get-detail-filter');

    //Po
    Route::get('/index-po', [PoPengajuanController::class, 'indexPoPengajuan'])->name('index.PO');
    Route::get('/index-po-depthead', [PoPengajuanController::class, 'indexPoDeptHead'])->name('index.PO.Dept');
    Route::get('/index-po-user', [PoPengajuanController::class, 'indexPoUser'])->name('index.PO.user');
    Route::get('/index-po-finance', [PoPengajuanController::class, 'indexPoFinance'])->name('index.PO.finance');
    Route::get('/index-po-procurement', [PoPengajuanController::class, 'indexPoProcurement'])->name('index.PO.procurement');
    Route::get('/index-po-procurement2', [PoPengajuanController::class, 'indexPoProcurement2'])->name('index.PO.procurement2');

    Route::get('/po-pengajuan/{id}/edit', [PoPengajuanController::class, 'edit'])->name('edit.PoPengajuan');
    Route::get('/po-pengajuan-dept/{id}/edit', [PoPengajuanController::class, 'editDept'])->name('edit.PoPengajuan.dept');

    Route::get('/create-po', [PoPengajuanController::class, 'createPoPengajuan'])->name('createPO');
    Route::get('/form-po/{id}', [PoPengajuanController::class, 'showFPBForm'])->name('view.FormPo');
    Route::get('/form-po-proc/{id}', [PoPengajuanController::class, 'showFPBProc'])->name('view.FormPo.proc');

    Route::post('/store-po', [PoPengajuanController::class, 'store'])->name('store.po');
    Route::put('/po-pengajuan/{id}', [PoPengajuanController::class, 'update'])->name('update.PoPengajuan');
    Route::put('/po-pengajuan-dept/{id}', [PoPengajuanController::class, 'updateDept'])->name('update.PoPengajuan.dept');
    Route::post('/update-status-by-fpb/{no_fpb}', [PoPengajuanController::class, 'updateStatusByNoFPB'])->name('kirim.fpb.secHead');
    Route::post('/update-FPB-DeptHead/{no_fpb}', [PoPengajuanController::class, 'updateStatusByDeptHead'])->name('kirim.fpb.deptHead');
    Route::post('/update-FPB-User/{no_fpb}', [PoPengajuanController::class, 'updateStatusByUser'])->name('kirim.fpb.user');
    Route::post('/update-FPB-Finance/{no_fpb}', [PoPengajuanController::class, 'updateStatusByFinance'])->name('kirim.fpb.finance');
    Route::post('/update-FPB-procurement/{no_fpb}', [PoPengajuanController::class, 'updateStatusByProcurement'])->name('kirim.fpb.procurement');
    Route::post('/update-FPB-progres/{id}', [PoPengajuanController::class, 'updateConfirmByProcurment'])->name('kirim.fpb.progres');
    Route::post('/cancel-item/{id}', [PoPengajuanController::class, 'updateCancelByProcurment'])->name('kirim.fpb.cancel');
    Route::post('/cancel-item2/{id}', [PoPengajuanController::class, 'updateCancelBySecHead'])->name('kirim.fpb.cancel2');
    Route::post('/po_pengajuan/finish/{no_fpb}', [PoPengajuanController::class, 'updateFinishByProcurment'])->name('update.PoPengajuan.finish');


    Route::get('/po-history/{no_fpb}', [PoPengajuanController::class, 'getPoHistory'])->name('po.history');
    Route::delete('/po_pengajuan/delete/{id}', [PoPengajuanController::class, 'deletePoPengajuan'])->name('delete.PoPengajuan');

    Route::get('/download-pdf-2/{no_fpb}', [PoPengajuanController::class, 'downloadPdfByNoFpb'])->name('download.pdf.2');
    Route::get('/download-file/{id}', [PoPengajuanController::class, 'downloadFile'])->name('download.file');
});
