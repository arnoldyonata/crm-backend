<?php

namespace App\Repositories;

use App\Models\FileBulkStarterPack;
use App\Models\Imsi;
use App\Models\Number;
use App\Models\Pack;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class FileBulkStarterPackRepository extends BaseRepository
{
    use TraitSelectUnprocessedRows;

    protected $imsi_types = [];

    protected $products = [];

    public function __construct()
    {
        parent::__construct(new FileBulkStarterPack());
    }

    public function getImsiTypeID(string $name)
    {
        if (empty($this->imsi_types)) {
            $this->imsi_types = \App\Models\ImsiType::all()->keyBy('name');
        }
        if (isset($this->imsi_types[$name])) {
            return $this->imsi_types[$name]->id;
        }
    }

    public function createImsi(FileBulkStarterPack $row): Imsi | null
    {
        \Illuminate\Support\Facades\Log::debug(__METHOD__);
        $exists = Imsi::query()
            ->where('imsi', $row->imsi)
            ->first();
        if ($exists) {
            return $exists;
        }

        return Imsi::create([
            'imsi' => $row->imsi,
            'imsi_status_id' => \App\Models\ImsiStatus::AVAILABLE,
            'imsi_type_id' => $row->imsi_type_id,
            'pin' => $row->pin,
            'puk_1' => $row->puk_1,
            'puk_2' => $row->puk_2,
            'ki' => $row->ki,
        ]);
    }

    public function selectImsisByImsi(array $imsis): Collection
    {
        return Imsi::query()
            ->whereIn('imsi', $imsis)
            ->get();
    }

    public function selectNumberByNumber(array $numbers): Collection
    {
        return Number::query()
            ->whereIn('number', $numbers)
            ->get();
    }

    public function createNumber(FileBulkStarterPack $row): Number | null
    {
        $exist = Number::query()
            ->where('number', $row->number)
            ->first();
        if ($exist) {
            return $exist;
        }

        return Number::create([
            'number' => $row->number,
            'number_type_id' => \App\Models\NumberType::PREPAID,
            'number_category_id' => \App\Models\NumberCategory::NORMAL,
            'number_status_id' => \App\Models\NumberStatus::AVAILABLE,
        ]);
    }

    public function getExistingImsis(Collection $rows): array
    {
        $imsis = $rows->pluck('imsi');
        $exists = $this->selectImsisByImsi($imsis->all());

        return $exists->pluck('imsi')->all();
    }

    public function getExistingNumber(Collection $rows): array
    {
        $numbers = $rows->pluck('number');
        $exists = $this->selectNumberByNumber($numbers->all());

        return $exists->pluck('number')->all();
    }

    public function checkExistingPack(FileBulkStarterPack $row)
    {
        return Pack::query()
            ->where('number_id', $row->number_id)
            ->orWhere('imsi_id', $row->imsi_id)
            ->count();
    }

    public function createPack(FileBulkStarterPack $row): Pack | null
    {
        $product = $this->getProductByName($row->product);

        return Pack::create([
            'number_id' => $row->number_id,
            'imsi_id' => $row->imsi_id,
            'product_id' => $product->id,
            'pack_type_id' => 1,
            'installation_date' => date('Y-m-d'),
            'expiry_date' => \Carbon\Carbon::now()->addYear(1)->format('Y-m-d'),
        ]);
    }

    public function getProductByName(string $name): Product| null
    {
        if (isset($this->products[$name])) {
            return $this->products[$name];
        }
        $product = Product::query()
            ->where('name', $name)
            ->first();

        return $this->products['name'] = $product;
    }
}
