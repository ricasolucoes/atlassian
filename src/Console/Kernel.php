<?php

namespace Atlassian\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel.
 *
 * @package Console
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\PhotoAtlassian\ChangeUserPassword::class,
        // Commands\PhotoAtlassian\User::class,
        // Commands\PhotoAtlassian\CreateRoles::class,
        // Commands\PhotoAtlassian\DeleteDetachedPhotosOlderThanWeek::class,
        // Commands\PhotoAtlassian\DeleteUnusedObjectsFromPhotoStorage::class,
        // Commands\PhotoAtlassian\GeneratePhotosMetadata::class,
        // Commands\PhotoAtlassian\GenerateRestApiDocumentation::class,
        // Commands\PhotoAtlassian\SendWeeklySubscriptionMails::class,
        // Commands\PhotoAtlassian\TestScheduler::class,

        // Commands\Photoacompanhante::class,


        // \Laravel\Tinker\Console\TinkerCommand::class,

        // /**
        //  * Me
        //  */
        // Commands\Explorer\InstagramGetAll::class,
        // Commands\Import\Data::class,
        // Commands\Import\Social::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    //     $schedule->command('horizon:snapshot')->everyFiveMinutes();


    //     $schedule->command(Commands\PhotoAtlassian\TestScheduler::class)
    //         ->hourly();

    //     $schedule->command(Commands\PhotoAtlassian\DeleteDetachedPhotosOlderThanWeek::class)
    //         ->dailyAt('00:00')
    //         ->onOneServer();

    //     $schedule->command(Commands\PhotoAtlassian\DeleteUnusedObjectsFromPhotoStorage::class)
    //         ->dailyAt('00:10')
    //         ->onOneServer();

    //     $schedule->command(Commands\PhotoAtlassian\SendWeeklySubscriptionMails::class)
    //         ->weekly()
    //         ->sundays()
    //         ->at('06:00')
    //         ->onOneServer();


    //     $schedule->command('import:photoacompanhante')
    //         ->hourly();



    //     $schedule->command(Commands\Tools\PhotoApp\TestScheduler::class)
    //     ->hourly();

    // $schedule->command(Commands\Tools\PhotoApp\DeleteDetachedPhotosOlderThanWeek::class)
    //     ->dailyAt('00:00')
    //     ->onOneServer();

    // $schedule->command(Commands\Tools\PhotoApp\DeleteUnusedObjectsFromPhotoStorage::class)
    //     ->dailyAt('00:10')
    //     ->onOneServer();

    // $schedule->command(Commands\Tools\PhotoApp\SendWeeklySubscriptionMails::class)
    //     ->weekly()
    //     ->sundays()
    //     ->at('06:00')
    //     ->onOneServer();

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        // include base_path('routes/console.php');
    }
}
