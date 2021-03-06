<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Invoice;

use App\Entity\Customer;
use App\Entity\InvoiceTemplate;
use App\Entity\Timesheet;
use App\Invoice\DefaultCalculator;
use App\Model\InvoiceModel;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Invoice\DefaultCalculator
 */
class DefaultCalculatorTest extends TestCase
{
    public function testEmptyModel()
    {
        $customer = new Customer();
        $template = new InvoiceTemplate();

        $model = new InvoiceModel();
        $model->setCustomer($customer);
        $model->setTemplate($template);

        $sut = new DefaultCalculator();
        $sut->setModel($model);

        $this->assertEquals(0, $sut->getTotal());
        $this->assertEquals(0, $sut->getVat());
        $this->assertEquals('EUR', $sut->getCurrency());
        $this->assertEquals(0, $sut->getSubtotal());
        $this->assertEquals(0, $sut->getTimeWorked());
        $this->assertEquals([], $sut->getEntries());
    }

    public function testWithMultipleEntries()
    {
        $customer = new Customer();
        $template = new InvoiceTemplate();
        $template->setVat(19);

        $timesheet = new Timesheet();
        $timesheet->setDuration(3600);
        $timesheet->setRate(293.27);

        $timesheet2 = new Timesheet();
        $timesheet2->setDuration(400);
        $timesheet2->setRate(84);

        $timesheet3 = new Timesheet();
        $timesheet3->setDuration(1800);
        $timesheet3->setRate(111.11);

        $entries = [$timesheet, $timesheet2, $timesheet3];

        $model = new InvoiceModel();
        $model->setCustomer($customer);
        $model->setTemplate($template);
        $model->setEntries($entries);

        $sut = new DefaultCalculator();
        $sut->setModel($model);

        $this->assertEquals(581.17, $sut->getTotal());
        $this->assertEquals(19, $sut->getVat());
        $this->assertEquals('EUR', $sut->getCurrency());
        $this->assertEquals(488.38, $sut->getSubtotal());
        $this->assertEquals(5800, $sut->getTimeWorked());
        $this->assertEquals($entries, $sut->getEntries());
    }
}
