<?php

namespace Tests\Unit;

use App\Models\PlannedExpense;
use App\Models\PlannedExpensePayment;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * Опиши сводно что тестируется, тезисно о каждом тесте
 *
 * Frequency is monthly and today is greater than day
 * Frequency is monthly and today is equal to day
 * Frequency is monthly and today is less than day
 *
 * Frequency is annually and today is greater than day and month is less than today
 * Frequency is annually and today is greater than day and month is equal to today
 * Frequency is annually and today is greater than day and month is greater than today
 *
 * Frequency is annually and today is less than day and month is less than today
 * Frequency is annually and today is less than day and month is equal to today
 * Frequency is annually and today is less than day and month is greater than today
 *
 * Frequency is annually and today is equal to day and month is less than today
 * Frequency is annually and today is equal to day and month is equal to today
 * Frequency is annually and today is equal to day and month is greater than today
 */
class PlannedExpenseTest extends TestCase
{
    /**
     * Test PlannedExpense getNearestDate method where frequency is monthly and today is greater than day
     * @return void
     */
    public function test_nearest_date_monthly_today_great(): void
    {
        $today = Carbon::parse('2024-02-20');
        $day = 15;
        $resultDate = '2024-03-15';

        $plan = new PlannedExpense([
            'amount' => 100,
            'day' => $day,
            'month' => 2,
            'frequency' => 'monthly',
            'currency_id' => 1,
            'category_id' => 1,
            'place_id' => 1,
            'user_id' => 1,
            'notes' => 'test',
        ]);

        $this->assertTrue(
            Carbon::parse($resultDate)->equalTo($plan->getNextPaymentDate($today))
        );
    }

    /**
     * Test PlannedExpense getNearestDate method where frequency is monthly and today is equal to day
     * @return void
     */
    public function test_nearest_date_monthly_today_equal(): void
    {
        $today = Carbon::parse('2024-02-15');
        $day = 15;
        $resultDate = '2024-02-15';

        $plan = new PlannedExpense([
            'amount' => 100,
            'day' => $day,
            'month' => 2,
            'frequency' => 'monthly',
            'currency_id' => 1,
            'category_id' => 1,
            'place_id' => 1,
            'user_id' => 1,
            'notes' => 'test',
        ]);

        $this->assertTrue(
            Carbon::parse($resultDate)->equalTo($plan->getNextPaymentDate($today))
        );
    }

    /**
     * Test PlannedExpense getNearestDate method where frequency is monthly and today is less than day without payment
     * @return void
     */
    public function test_nearest_date_monthly_today_less(): void
    {
        $today = Carbon::parse('2024-02-10');
        $day = 15;
        $resultDate = '2024-02-15';

        $plan = new PlannedExpense([
            'amount' => 100,
            'day' => $day,
            'month' => 2,
            'frequency' => 'monthly',
            'currency_id' => 1,
            'category_id' => 1,
            'place_id' => 1,
            'user_id' => 1,
            'notes' => 'test',
        ]);

        $this->assertTrue(
            Carbon::parse($resultDate)->equalTo($plan->getNextPaymentDate($today))
        );
    }

    /**
     * Test PlannedExpense getNearestDate method where frequency is annually and today is greater than day and month is less than today
     * @return void
     */
    public function test_nearest_date_annually_today_great_month_less(): void
    {
        $today = Carbon::parse('2024-02-20');
        $day = 15;
        $month = 1;
        $resultDate = '2025-01-15';

        $plan = new PlannedExpense([
            'amount' => 100,
            'day' => $day,
            'month' => $month,
            'frequency' => 'annually',
            'currency_id' => 1,
            'category_id' => 1,
            'place_id' => 1,
            'user_id' => 1,
            'notes' => 'test',
        ]);

        $this->assertTrue(
            Carbon::parse($resultDate)->equalTo($plan->getNextPaymentDate($today))
        );
    }

    /**
     * Test PlannedExpense getNearestDate method where frequency is annually and today is greater than day and month is equal to today
     * @return void
     */
    public function test_nearest_date_annually_today_great_month_equal(): void
    {
        $today = Carbon::parse('2024-02-20');
        $day = 15;
        $month = 2;
        $resultDate = '2025-02-15';

        $plan = new PlannedExpense([
            'amount' => 100,
            'day' => $day,
            'month' => $month,
            'frequency' => 'annually',
            'currency_id' => 1,
            'category_id' => 1,
            'place_id' => 1,
            'user_id' => 1,
            'notes' => 'test',
        ]);

        $this->assertTrue(
            Carbon::parse($resultDate)->equalTo($plan->getNextPaymentDate($today))
        );
    }

    /**
     * Test PlannedExpense getNearestDate method where frequency is annually and today is greater than day and month is greater than today
     * @return void
     */
    public function test_nearest_date_annually_today_greater_month_great(): void
    {
        $today = Carbon::parse('2024-01-20');
        $day = 15;
        $month = 2;
        $resultDate = '2025-02-15';

        $plan = new PlannedExpense([
            'amount' => 100,
            'day' => $day,
            'month' => $month,
            'frequency' => 'annually',
            'currency_id' => 1,
            'category_id' => 1,
            'place_id' => 1,
            'user_id' => 1,
            'notes' => 'test',
        ]);

        echo $plan->getNextPaymentDate($today);

        $this->assertTrue(
            Carbon::parse($resultDate)->equalTo($plan->getNextPaymentDate($today))
        );
    }

    /**
     * Test PlannedExpense getNearestDate method where frequency is annually and today is less than day and month is less than today
     * @return void
     */
    public function test_nearest_date_annually_today_less_month_less(): void
    {
        $today = Carbon::parse('2024-02-10');
        $day = 15;
        $month = 1;
        $resultDate = '2025-01-15';

        $plan = new PlannedExpense([
            'amount' => 100,
            'day' => $day,
            'month' => $month,
            'frequency' => 'annually',
            'currency_id' => 1,
            'category_id' => 1,
            'place_id' => 1,
            'user_id' => 1,
            'notes' => 'test',
        ]);

        $this->assertTrue(
            Carbon::parse($resultDate)->equalTo($plan->getNextPaymentDate($today))
        );
    }

    /**
     * Test PlannedExpense getNearestDate method where frequency is annually and today is less than day and month is equal to today
     * @return void
     */
    public function test_nearest_date_annually_today_less_month_equal(): void
    {
        $today = Carbon::parse('2024-02-10');
        $day = 15;
        $month = 2;
        $resultDate = '2024-02-15';

        $plan = new PlannedExpense([
            'amount' => 100,
            'day' => $day,
            'month' => $month,
            'frequency' => 'annually',
            'currency_id' => 1,
            'category_id' => 1,
            'place_id' => 1,
            'user_id' => 1,
            'notes' => 'test',
        ]);

        $this->assertTrue(
            Carbon::parse($resultDate)->equalTo($plan->getNextPaymentDate($today))
        );
    }

    /**
     * Test PlannedExpense getNearestDate method where frequency is annually and today is less than day and month is greater than today
     * @return void
     */
    public function test_nearest_date_annually_today_less_month_great(): void
    {
        $today = Carbon::parse('2024-02-10');
        $day = 15;
        $month = 3;
        $resultDate = '2024-03-15';

        $plan = new PlannedExpense([
            'amount' => 100,
            'day' => $day,
            'month' => $month,
            'frequency' => 'annually',
            'currency_id' => 1,
            'category_id' => 1,
            'place_id' => 1,
            'user_id' => 1,
            'notes' => 'test',
        ]);

        echo $plan->getNextPaymentDate($today);

        $this->assertTrue(
            Carbon::parse($resultDate)->equalTo($plan->getNextPaymentDate($today))
        );
    }

    /**
     * Test PlannedExpense getNearestDate method where frequency is annually and today is equal to day and month is less than today
     * @return void
     */
    public function test_nearest_date_annually_today_equal_month_less(): void
    {
        $today = Carbon::parse('2024-02-15');
        $day = 15;
        $month = 1;
        $resultDate = '2025-01-15';

        $plan = new PlannedExpense([
            'amount' => 100,
            'day' => $day,
            'month' => $month,
            'frequency' => 'annually',
            'currency_id' => 1,
            'category_id' => 1,
            'place_id' => 1,
            'user_id' => 1,
            'notes' => 'test',
        ]);

        $this->assertTrue(
            Carbon::parse($resultDate)->equalTo($plan->getNextPaymentDate($today))
        );
    }

    /**
     * Test PlannedExpense getNearestDate method where frequency is annually and today is equal to day and month is equal to today
     * @return void
     */
    public function test_nearest_date_annually_today_equal_month_equal(): void
    {
        $today = Carbon::parse('2024-02-15');
        $day = 15;
        $month = 2;
        $resultDate = '2024-02-15';

        $plan = new PlannedExpense([
            'amount' => 100,
            'day' => $day,
            'month' => $month,
            'frequency' => 'annually',
            'currency_id' => 1,
            'category_id' => 1,
            'place_id' => 1,
            'user_id' => 1,
            'notes' => 'test',
        ]);

        $this->assertTrue(
            Carbon::parse($resultDate)->equalTo($plan->getNextPaymentDate($today))
        );
    }

    /**
     * Test PlannedExpense getNearestDate method where frequency is annually and today is equal to day and month is greater than today
     * @return void
     */
    public function test_nearest_date_annually_today_equal_month_great(): void
    {
        $today = Carbon::parse('2024-02-15');
        $day = 15;
        $month = 3;
        $resultDate = '2024-03-15';

        $plan = new PlannedExpense([
            'amount' => 100,
            'day' => $day,
            'month' => $month,
            'frequency' => 'annually',
            'currency_id' => 1,
            'category_id' => 1,
            'place_id' => 1,
            'user_id' => 1,
            'notes' => 'test',
        ]);

        $this->assertTrue(
            Carbon::parse($resultDate)->equalTo($plan->getNextPaymentDate($today))
        );
    }
}
