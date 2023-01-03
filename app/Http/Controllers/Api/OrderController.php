<?php

namespace App\Http\Controllers\Api;

use App\Services\OrderService;
use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderRequest;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @param OrderRequest $request
     * @return JsonResponse
     */
    public function order(OrderRequest $request): object
    {
        $response = DB::transaction(function () use ($request){
            $products = Product::whereIn('id', array_column($request->get('products'), 'product_id'))
                ->with(['ingredients' => function ($query){
                    $query->withSum('stocks', 'stock')->with('stocks');
                }])
                ->get();

            // Accepts the order details from the request payload.
            if ($products->isNotEmpty()) {
                $orderDetail = $this->orderService->acceptOrderDetail($products, $request);

                $orderProducts = $orderDetail['products'];
                $errors = $orderDetail['errors'];
                $attachedArray = $orderDetail['attachedArray'];

                if ($orderProducts->isNotEmpty()) {
                    // Persists the Order in the database.
                    $this->orderService->saveOrder($attachedArray);

                    // stock Update
                    $this->orderService->stockUpdate($orderProducts, $attachedArray);
                }

                return $errors;
            }
        });

        return response()->json([
            'success' => ($response && (count($response) > 0)) ? false : true,
            'message' => ($response && (count($response) > 0)) ? $response : 'Order saved successfully.'
        ]);
    }
}
