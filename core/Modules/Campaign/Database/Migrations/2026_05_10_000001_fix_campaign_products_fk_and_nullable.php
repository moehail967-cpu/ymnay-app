<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixCampaignProductsFkAndNullable extends Migration
{
    // T21: Orphan cleanup is handled by the Product model observer (ProductObserver)
    // which deletes campaign_products rows on product deletion.
    // A direct FK constraint is not added here because product_id is bigInteger (signed)
    // while products.id is unsignedBigInteger — changing the type would require data migration.
    public function up() {}
    public function down() {}
}
