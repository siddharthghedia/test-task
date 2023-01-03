<?php

namespace App\Listeners;

use App\Events\IngredientsStockHalf;
use App\Mail\HalfStockOfIngredient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailToMerchant implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(IngredientsStockHalf $event)
    {
        Mail::to(env('MERCHANT_MAIL'))->send(new HalfStockOfIngredient($event->ingredient));
    }
}
