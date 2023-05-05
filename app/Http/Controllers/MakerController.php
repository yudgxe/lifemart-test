<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;;


class MakerController extends Controller
{
    function combinations($n, $k) {
        $buffer = array();
        $result = array();
    
        for ($i = 0; $i < $k; $i++) {
            array_push($buffer, $i);
        }
        array_push($buffer, $n, 0);
    
        for(;;) {
            array_push($result, array_slice($buffer, 0, $k));
            $j = 0;
            for (; $buffer[$j] + 1 == $buffer[$j + 1];) {
                $buffer[$j] = $j;
                $j++;
            }
            if ($j < $k) {
                $buffer[$j]++;
            } else {
                break;
            }
        }
        return $result;
    }
    
    function adding($a, $b) {
        $sizeA = count($a);
        if($sizeA == 0) {
            return $b;
        }   
        $sizeB = count($b);
        $result = array();
        for($i = 0; $i < $sizeA * $sizeB; $i++) {
            $result[$i] = [
                'product' => array_merge($a[intdiv($i, $sizeB)]['product'], $b[$i % $sizeB]['product']),
                'price' => $a[intdiv($i, $sizeB)]['price'] + $b[$i % $sizeB]['price'],
            ];
        }
    
        return $result;
    }
    
    
    public function MakePizza(string  $template) {
        $ingredients = DB::table('ingredient')
                        ->select(DB::raw('ingredient.title as value, price, ingredient_type.title as type, code'))
                        ->join('ingredient_type', 'ingredient.type_id', '=', 'ingredient_type.id')
                        ->get();

        
        $ingredients = $ingredients->mapToGroups(function ($item, $key) {
            return [$item->code => $item];
        });

        $respoceSize = 1;
        $result = array();

        foreach (count_chars($template, 1) as $i => $val) {
            $char = chr($i);
            if (!$ingredients->has($char)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'incorrect code '. utf8_encode($char),
                ], 400);
            }

            if ($ingredients[$char]->count() < $val) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'getting an incorrect length for symbol '.$char.' received '.$val.' expected '.$ingredients[$char]->count(),
                ], 400);
            }

            $combs = $this->combinations($ingredients[$char]->count(), $val);

            for ($i = 0; $i < count($combs); $i++) {
                $tmp = array();
                $price = 0;
                for ($j = 0; $j < count($combs[$i]); $j++) {
                    array_push($tmp, [
                        'type' => $ingredients[$char][$combs[$i][$j]]->type,
                        'value' => $ingredients[$char][$combs[$i][$j]]->value,
                    ]);

                    $price += $ingredients[$char][$combs[$i][$j]]->price;
                }

                $combs[$i] = [
                    'product' => $tmp,
                    'price' => $price,
                ];
            }

            $result = $this->adding($result, $combs);
        }

        return response()->json([
            'status' => 'succes',
            'data' =>$result,
        ]);
    }
}
