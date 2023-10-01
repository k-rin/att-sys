<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\LeaveReport;
use App\Enums\LeaveType;
use App\Enums\ReportStatus;

class CreateLeaveReportLimitTest extends TestCase
{
    /**
     * 執行方法
     * php artisan test tests/Feature/Api/CreateLeaveReportLimitTest.php
     * 
     * 需匯入calendar表資料
     */

    use DatabaseTransactions;

    const USER = [
        'email' => 'test19547@test.com',
        'name' => 'abc',
        'alias' => 'a',
        'birthday' => '1994-07-08',
        'hire_date' => '2022-10-10',
        'paid_leaves' => 7,
        'department_id' => 1,
        'locked' => 0,
    ];
    const JwtTOKEN = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6InRlc3QxOTU0N0B0ZXN0LmNvbSJ9.u-RNDrBz6ll7edDfHm9j3726O_43bFnuwKNInneF2QM';
    private $userId;
    public function setUp(): void
    {
        parent::setUp();

        // users塞入測試資料
        $user = User::create(self::USER);
        $this->userId = $user->id;
    }

    private function execute(array $leaveReport)
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.self::JwtTOKEN,
        ])->post('/api/v1/users/me/leave-reports', $leaveReport);

        return $response;
    }

    private function successCase($response, $leaveReport)
    {
        $response->assertStatus(201);
        $this->assertDatabaseHas('leave_reports', [
            'user_id' => $this->userId,
            'start_at' => $leaveReport['start_at'],
            'end_at' => $leaveReport['end_at'],
            'type' => $leaveReport['type'],
        ]);
    }

    private function failCase($response)
    {
        $response->assertStatus(500);
        $response->assertSee('超過請假天數上限');
    }

    // 請事假成功
    public function testPersonLeaveSccess()
    {
        $leaveReport = [
            'start_at' => '2022-10-11 09:00:00',
            'end_at' => '2022-10-11 18:00:00',
            'type' => LeaveType::PersonLeave,
            'reason' => '',
        ];

        $response = $this->execute($leaveReport);

        $this->successCase($response, $leaveReport);
    }

    // 本身請假天數超過
    public function testSelfFail()
    {
        $leaveReport = [
            'start_at' => '2022-10-11 09:00:00',
            'end_at' => '2022-10-31 18:00:00',
            'type' => LeaveType::PersonLeave,
            'reason' => '',
        ];

        $response = $this->execute($leaveReport);

        $this->failCase($response);
    }

    // 本身請假天數跨年成功
    public function testSelfSpanYearSuccess()
    {
        $leaveReport = [
            'start_at' => '2022-12-29 09:00:00',
            'end_at' => '2023-01-18 18:00:00',
            'type' => LeaveType::PersonLeave,
            'reason' => '',
        ];

        $response = $this->execute($leaveReport);

        $this->successCase($response, $leaveReport);
    }

    // 本身請假天數跨年失敗
    public function testSelfSpanYearFail()
    {
        $leaveReport = [
            'start_at' => '2022-12-29 09:00:00',
            'end_at' => '2023-01-20 18:00:00',
            'type' => LeaveType::PersonLeave,
            'reason' => '',
        ];

        $response = $this->execute($leaveReport);

        $this->failCase($response);
    }

    public function testFailCase1()
    {
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-09-30 09:00:00',
            'end_at' => '2022-10-20 18:00:00',
            'days' => 14,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);

        $leaveReport = [
            'start_at' => '2022-10-22 09:00:00',
            'end_at' => '2022-10-22 18:00:00',
            'type' => LeaveType::PersonLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->failCase($response);
    }

    public function testFailCase2()
    {
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-07-04 09:00:00',
            'end_at' => '2022-07-12 18:00:00',
            'days' => 7,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-08-01 09:00:00',
            'end_at' => '2022-08-09 18:00:00',
            'days' => 7,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);

        $leaveReport = [
            'start_at' => '2022-10-20 09:00:00',
            'end_at' => '2022-10-20 18:00:00',
            'type' => LeaveType::PersonLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->failCase($response);
    }

    public function testFailCase3()
    {
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2021-12-31 09:00:00',
            'end_at' => '2022-01-07 18:00:00',
            'days' => 6,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-12-20 09:00:00',
            'end_at' => '2023-01-02 18:00:00',
            'days' => 10,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);

        $leaveReport = [
            'start_at' => '2022-10-20 09:00:00',
            'end_at' => '2022-10-20 18:00:00',
            'type' => LeaveType::PersonLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->failCase($response);
    }

    public function testFailCase4()
    {
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-07-04 09:00:00',
            'end_at' => '2022-07-12 18:00:00',
            'days' => 7,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-08-01 09:00:00',
            'end_at' => '2022-08-09 18:00:00',
            'days' => 7,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);

        $leaveReport = [
            'start_at' => '2022-12-31 09:00:00',
            'end_at' => '2023-01-02 18:00:00',
            'type' => LeaveType::PersonLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->failCase($response);
    }

    public function testSuccessCase1()
    {
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-10-03 09:00:00',
            'end_at' => '2022-10-19 18:00:00',
            'days' => 13,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);

        $leaveReport = [
            'start_at' => '2022-10-21 09:00:00',
            'end_at' => '2022-10-21 18:00:00',
            'type' => LeaveType::PersonLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->successCase($response, $leaveReport);
    }

    public function testSuccessCase2()
    {
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-08-01 09:00:00',
            'end_at' => '2022-08-04 18:00:00',
            'days' => 4,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-09-05 09:00:00',
            'end_at' => '2022-09-09 18:00:00',
            'days' => 5,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);

        $leaveReport = [
            'start_at' => '2022-10-20 09:00:00',
            'end_at' => '2022-10-21 18:00:00',
            'type' => LeaveType::PersonLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->successCase($response, $leaveReport);
    }

    public function testSuccessCase3()
    {
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2021-12-29 09:00:00',
            'end_at' => '2022-01-05 18:00:00',
            'days' => 6,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-12-27 09:00:00',
            'end_at' => '2023-01-03 18:00:00',
            'days' => 6,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);

        $leaveReport = [
            'start_at' => '2022-10-20 09:00:00',
            'end_at' => '2022-10-21 18:00:00',
            'type' => LeaveType::PersonLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->successCase($response, $leaveReport);
    }

    public function testSuccessCase4()
    {
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-08-03 09:00:00',
            'end_at' => '2022-08-05 18:00:00',
            'days' => 3,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-09-07 09:00:00',
            'end_at' => '2022-09-09 18:00:00',
            'days' => 3,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);

        $leaveReport = [
            'start_at' => '2022-12-26 09:00:00',
            'end_at' => '2023-01-07 18:00:00',
            'type' => LeaveType::PersonLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->successCase($response, $leaveReport);
    }

    // 家庭照顧假併入事假計算
    public function testFamilyCareLeaveFailCase()
    {
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-07-04 09:00:00',
            'end_at' => '2022-07-12 18:00:00',
            'days' => 7,
            'type' => LeaveType::FamilyCareLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-08-01 09:00:00',
            'end_at' => '2022-08-09 18:00:00',
            'days' => 7,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);

        $leaveReport = [
            'start_at' => '2022-10-20 09:00:00',
            'end_at' => '2022-10-20 18:00:00',
            'type' => LeaveType::PersonLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->failCase($response);
    }

    public function testFamilyCareLeaveSuccessCase()
    {
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-08-01 09:00:00',
            'end_at' => '2022-08-05 18:00:00',
            'days' => 5,
            'type' => LeaveType::PersonLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-09-05 09:00:00',
            'end_at' => '2022-09-09 18:00:00',
            'days' => 5,
            'type' => LeaveType::FamilyCareLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);

        $leaveReport = [
            'start_at' => '2022-10-20 09:00:00',
            'end_at' => '2022-10-21 18:00:00',
            'type' => LeaveType::PersonLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->successCase($response, $leaveReport);
    }

    //生理假每月1天
    public function testMenstrualLeaveFailCase1()
    {
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-08-01 09:00:00',
            'end_at' => '2022-08-01 18:00:00',
            'days' => 1,
            'type' => LeaveType::MenstrualLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);

        $leaveReport = [
            'start_at' => '2022-08-31 09:00:00',
            'end_at' => '2022-08-31 18:00:00',
            'type' => LeaveType::MenstrualLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->failCase($response);
    }

    public function testMenstrualLeaveFailCase2()
    {
        $leaveReport = [
            'start_at' => '2022-08-30 09:00:00',
            'end_at' => '2022-08-31 18:00:00',
            'type' => LeaveType::MenstrualLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->failCase($response);
    }

    public function testMenstrualLeaveSuccessCase1()
    {
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-07-29 09:00:00',
            'end_at' => '2022-07-29 18:00:00',
            'days' => 1,
            'type' => LeaveType::MenstrualLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);

        $leaveReport = [
            'start_at' => '2022-08-01 09:00:00',
            'end_at' => '2022-08-01 18:00:00',
            'type' => LeaveType::MenstrualLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->successCase($response, $leaveReport);
    }

    public function testMenstrualLeaveSuccessCase2()
    {
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-08-01 09:00:00',
            'end_at' => '2022-08-01 13:00:00',
            'days' => 0.5,
            'type' => LeaveType::MenstrualLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);

        $leaveReport = [
            'start_at' => '2022-08-31 09:00:00',
            'end_at' => '2022-08-31 13:00:00',
            'type' => LeaveType::MenstrualLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->successCase($response, $leaveReport);
    }

    //病假超過上限還是可以請
    public function testSickLeaveCase()
    {
        $leaveReportOld = [
            'user_id' => $this->userId,
            'start_at' => '2022-08-01 09:00:00',
            'end_at' => '2022-09-09 18:00:00',
            'days' => 30,
            'type' => LeaveType::SickLeave,
            'reason' => '',
            'status' => ReportStatus::Permited,
        ];
        LeaveReport::create($leaveReportOld);

        $leaveReport = [
            'start_at' => '2022-09-28 09:00:00',
            'end_at' => '2022-09-28 18:00:00',
            'type' => LeaveType::SickLeave,
            'reason' => '',
        ];
        $response = $this->execute($leaveReport);
        $this->successCase($response, $leaveReport);
    }
}
