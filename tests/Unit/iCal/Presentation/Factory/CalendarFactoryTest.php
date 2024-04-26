<?php

namespace Tests\Unit\iCal\Presentation\Factory;

use App\iCal\Domain\Entity\Calendar;
use App\iCal\Domain\Enum\CalendarMethod;
use App\iCal\Presentation\Factory\CalendarFactory;
use Eluceo\iCal\Domain\Entity\TimeZone;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CalendarFactoryTest extends TestCase
{
    public function test_description_is_rendered(): void
    {
        $description = fake()->sentence(nbWords: 2);

        $calendar = new Calendar();
        $calendar->setDescription($description);

        $factory = new CalendarFactory();
        $output = $factory->createCalendar($calendar);

        $this->assertStringContainsString("X-WR-CALDESC:$description\r\n", (string) $output);
    }

    #[DataProvider('method_output_provider')]
    public function test_method_is_rendered(CalendarMethod $method, string $expected): void
    {
        $calendar = new Calendar();
        $calendar->setMethod($method);

        $factory = new CalendarFactory();
        $output = $factory->createCalendar($calendar);

        $this->assertStringContainsString("METHOD:$expected\r\n", (string) $output);
    }

    public static function method_output_provider(): array
    {
        return [
            [CalendarMethod::Add, 'ADD'],
            [CalendarMethod::Cancel, 'CANCEL'],
            [CalendarMethod::Counter, 'COUNTER'],
            [CalendarMethod::DeclineCounter, 'DECLINECOUNTER'],
            [CalendarMethod::Publish, 'PUBLISH'],
            [CalendarMethod::Refresh, 'REFRESH'],
            [CalendarMethod::Reply, 'REPLY'],
            [CalendarMethod::Request, 'REQUEST'],
        ];
    }

    public function test_name_is_rendered(): void
    {
        $name = fake()->sentence(nbWords: 2);

        $calendar = new Calendar();
        $calendar->setName($name);

        $factory = new CalendarFactory();
        $output = $factory->createCalendar($calendar);

        $this->assertStringContainsString("X-WR-CALNAME:$name\r\n", (string) $output);
    }

    public function test_time_zone_is_rendered(): void
    {
        $timezone = fake()->timezone();

        $calendar = new Calendar();
        $calendar->setTimeZone(new TimeZone($timezone));

        $factory = new CalendarFactory();
        $output = $factory->createCalendar($calendar);

        $this->assertStringContainsString("X-WR-TIMEZONE:$timezone\r\n", (string) $output);
    }
}
