<?php

use Illuminate\Database\Seeder;

class SeedDataProduct extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->truncate();

        \App\Model\Common\File::query()
            ->whereIn('model_id', range(1,9))
            ->where('model_type', \App\Model\Admin\Product::class)->delete();

        \App\Model\Admin\ProductGallery::query()->whereIn('product_id', [range(1,9)])->delete();

        DB::table('products')->insert([
            [
               'id' => 1,
               'name' => 'Bếp ga âm ABC',
               'status' => 1,
               'created_by' => 1,
               'updated_by' => 1,
               'price' => 600000,
               'cate_id' => 6,
               'body'=>'<div><strong>The High Efficiency 1-Inch Water Softener is designed to provide:</strong>
<ul>
	<li>A smart brine tank automatically monitors salt levels and tells you how many days of salt are remaining. Plus, an easy-to-read display provides system alerts and lets you know your softener is working.</li>
	<li><strong>Convenience</strong>The automatic bypass valve provides the convenience of skipping the water softener or the remote display</li>
	<li><strong>Reliability</strong>A specialized, non-corrosive valve provides more reliability than rotary valve systems. Culligan also offers a high-impact Quadra Hull<sup>®</sup> Tank designed for outdoor use with a four-layer design that resists UV rays, rust, and corrosion.</li>
	<li><strong>Worry-Free Maintenance</strong>An automatic Service Notification to your local Culligan Water Expert takes the hassle and guesswork out of maintenance.</li>
</ul>
</div>',
               'intro' => '<p>The Culligan High Efficiency Water Softener brings you better, softer water and can help prolong the lifespan and efficiency of your appliances, heating and plumbing systems.</p>

<p>The HE Softener features Aqua-Sensors® and smart meter components and automatically adjusts to changes in your home’s water conditions. It only regenerates when needed, and gives you the chance to control and customize water softness throughout your house.</p>',
               'slug' => 'bep-ga-am-abc',
               'manufacturer_id' => 1,
               'origin_id' => 1,
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now(),
           ],
            [
                'id' => 2,
                'name' => 'Bếp ga âm DRF',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'price' => 620000,
                'cate_id' => 6,
                'body'=>'<div><strong>The High Efficiency 1-Inch Water Softener is designed to provide:</strong>
<ul>
	<li>A smart brine tank automatically monitors salt levels and tells you how many days of salt are remaining. Plus, an easy-to-read display provides system alerts and lets you know your softener is working.</li>
	<li><strong>Convenience</strong>The automatic bypass valve provides the convenience of skipping the water softener or the remote display</li>
	<li><strong>Reliability</strong>A specialized, non-corrosive valve provides more reliability than rotary valve systems. Culligan also offers a high-impact Quadra Hull<sup>®</sup> Tank designed for outdoor use with a four-layer design that resists UV rays, rust, and corrosion.</li>
	<li><strong>Worry-Free Maintenance</strong>An automatic Service Notification to your local Culligan Water Expert takes the hassle and guesswork out of maintenance.</li>
</ul>
</div>',
                'intro' => '<p>The Culligan High Efficiency Water Softener brings you better, softer water and can help prolong the lifespan and efficiency of your appliances, heating and plumbing systems.</p>

<p>The HE Softener features Aqua-Sensors® and smart meter components and automatically adjusts to changes in your home’s water conditions. It only regenerates when needed, and gives you the chance to control and customize water softness throughout your house.</p>',
                'slug' => 'bep-ga-am-drf',
                'manufacturer_id' => 1,
                'origin_id' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 3,
                'name' => 'Bếp ga âm JHN',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'price' => 600000,
                'cate_id' => 6,
                'body'=>'<div><strong>The High Efficiency 1-Inch Water Softener is designed to provide:</strong>
<ul>
	<li>A smart brine tank automatically monitors salt levels and tells you how many days of salt are remaining. Plus, an easy-to-read display provides system alerts and lets you know your softener is working.</li>
	<li><strong>Convenience</strong>The automatic bypass valve provides the convenience of skipping the water softener or the remote display</li>
	<li><strong>Reliability</strong>A specialized, non-corrosive valve provides more reliability than rotary valve systems. Culligan also offers a high-impact Quadra Hull<sup>®</sup> Tank designed for outdoor use with a four-layer design that resists UV rays, rust, and corrosion.</li>
	<li><strong>Worry-Free Maintenance</strong>An automatic Service Notification to your local Culligan Water Expert takes the hassle and guesswork out of maintenance.</li>
</ul>
</div>',
                'intro' => '<p>The Culligan High Efficiency Water Softener brings you better, softer water and can help prolong the lifespan and efficiency of your appliances, heating and plumbing systems.</p>

<p>The HE Softener features Aqua-Sensors® and smart meter components and automatically adjusts to changes in your home’s water conditions. It only regenerates when needed, and gives you the chance to control and customize water softness throughout your house.</p>',
                'slug' => 'bep-ga-am-jhl',
                'manufacturer_id' => 3,
                'origin_id' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],

            [
                'id' => 4,
                'name' => 'Bếp ga hồng ngoại AAA',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'price' => 840000,
                'cate_id' => 7,
                'body'=>'<div><strong>The High Efficiency 1-Inch Water Softener is designed to provide:</strong>
<ul>
	<li>A smart brine tank automatically monitors salt levels and tells you how many days of salt are remaining. Plus, an easy-to-read display provides system alerts and lets you know your softener is working.</li>
	<li><strong>Convenience</strong>The automatic bypass valve provides the convenience of skipping the water softener or the remote display</li>
	<li><strong>Reliability</strong>A specialized, non-corrosive valve provides more reliability than rotary valve systems. Culligan also offers a high-impact Quadra Hull<sup>®</sup> Tank designed for outdoor use with a four-layer design that resists UV rays, rust, and corrosion.</li>
	<li><strong>Worry-Free Maintenance</strong>An automatic Service Notification to your local Culligan Water Expert takes the hassle and guesswork out of maintenance.</li>
</ul>
</div>',
                'intro' => '<p>The Culligan High Efficiency Water Softener brings you better, softer water and can help prolong the lifespan and efficiency of your appliances, heating and plumbing systems.</p>

<p>The HE Softener features Aqua-Sensors® and smart meter components and automatically adjusts to changes in your home’s water conditions. It only regenerates when needed, and gives you the chance to control and customize water softness throughout your house.</p>',
                'slug' => 'bep-ga-hong-ngoai-aaa',
                'manufacturer_id' => 3,
                'origin_id' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 5,
                'name' => 'Bếp ga hồng ngoại DDD',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'price' => 810000,
                'cate_id' => 7,
                'body'=>'<div><strong>The High Efficiency 1-Inch Water Softener is designed to provide:</strong>
<ul>
	<li>A smart brine tank automatically monitors salt levels and tells you how many days of salt are remaining. Plus, an easy-to-read display provides system alerts and lets you know your softener is working.</li>
	<li><strong>Convenience</strong>The automatic bypass valve provides the convenience of skipping the water softener or the remote display</li>
	<li><strong>Reliability</strong>A specialized, non-corrosive valve provides more reliability than rotary valve systems. Culligan also offers a high-impact Quadra Hull<sup>®</sup> Tank designed for outdoor use with a four-layer design that resists UV rays, rust, and corrosion.</li>
	<li><strong>Worry-Free Maintenance</strong>An automatic Service Notification to your local Culligan Water Expert takes the hassle and guesswork out of maintenance.</li>
</ul>
</div>',
                'intro' => '<p>The Culligan High Efficiency Water Softener brings you better, softer water and can help prolong the lifespan and efficiency of your appliances, heating and plumbing systems.</p>

<p>The HE Softener features Aqua-Sensors® and smart meter components and automatically adjusts to changes in your home’s water conditions. It only regenerates when needed, and gives you the chance to control and customize water softness throughout your house.</p>',
                'slug' => 'bep-ga-hong-ngoai-ddd',
                'manufacturer_id' => 3,
                'origin_id' => 2,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],

            [
                'id' => 6,
                'name' => 'Bếp ga hồng ngoại ZZZ',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'price' => 890000,
                'cate_id' => 7,
                'body'=>'<div><strong>The High Efficiency 1-Inch Water Softener is designed to provide:</strong>
<ul>
	<li>A smart brine tank automatically monitors salt levels and tells you how many days of salt are remaining. Plus, an easy-to-read display provides system alerts and lets you know your softener is working.</li>
	<li><strong>Convenience</strong>The automatic bypass valve provides the convenience of skipping the water softener or the remote display</li>
	<li><strong>Reliability</strong>A specialized, non-corrosive valve provides more reliability than rotary valve systems. Culligan also offers a high-impact Quadra Hull<sup>®</sup> Tank designed for outdoor use with a four-layer design that resists UV rays, rust, and corrosion.</li>
	<li><strong>Worry-Free Maintenance</strong>An automatic Service Notification to your local Culligan Water Expert takes the hassle and guesswork out of maintenance.</li>
</ul>
</div>',
                'intro' => '<p>The Culligan High Efficiency Water Softener brings you better, softer water and can help prolong the lifespan and efficiency of your appliances, heating and plumbing systems.</p>

<p>The HE Softener features Aqua-Sensors® and smart meter components and automatically adjusts to changes in your home’s water conditions. It only regenerates when needed, and gives you the chance to control and customize water softness throughout your house.</p>',
                'slug' => 'bep-ga-hong-ngoai-zzz',
                'manufacturer_id' => 4,
                'origin_id' => 2,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],

            [
                'id' => 7,
                'name' => 'Bếp điện từ IOIO',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'price' => 520000,
                'cate_id' => 2,
                'body'=>'<div><strong>The High Efficiency 1-Inch Water Softener is designed to provide:</strong>
<ul>
	<li>A smart brine tank automatically monitors salt levels and tells you how many days of salt are remaining. Plus, an easy-to-read display provides system alerts and lets you know your softener is working.</li>
	<li><strong>Convenience</strong>The automatic bypass valve provides the convenience of skipping the water softener or the remote display</li>
	<li><strong>Reliability</strong>A specialized, non-corrosive valve provides more reliability than rotary valve systems. Culligan also offers a high-impact Quadra Hull<sup>®</sup> Tank designed for outdoor use with a four-layer design that resists UV rays, rust, and corrosion.</li>
	<li><strong>Worry-Free Maintenance</strong>An automatic Service Notification to your local Culligan Water Expert takes the hassle and guesswork out of maintenance.</li>
</ul>
</div>',
                'intro' => '<p>The Culligan High Efficiency Water Softener brings you better, softer water and can help prolong the lifespan and efficiency of your appliances, heating and plumbing systems.</p>

<p>The HE Softener features Aqua-Sensors® and smart meter components and automatically adjusts to changes in your home’s water conditions. It only regenerates when needed, and gives you the chance to control and customize water softness throughout your house.</p>',
                'slug' => 'bep-dien-tu-ioioi',
                'manufacturer_id' => 4,
                'origin_id' => 2,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 8,
                'name' => 'Bếp điện từ NNN',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'price' => 525000,
                'cate_id' => 2,
                'body'=>'<div><strong>The High Efficiency 1-Inch Water Softener is designed to provide:</strong>
<ul>
	<li>A smart brine tank automatically monitors salt levels and tells you how many days of salt are remaining. Plus, an easy-to-read display provides system alerts and lets you know your softener is working.</li>
	<li><strong>Convenience</strong>The automatic bypass valve provides the convenience of skipping the water softener or the remote display</li>
	<li><strong>Reliability</strong>A specialized, non-corrosive valve provides more reliability than rotary valve systems. Culligan also offers a high-impact Quadra Hull<sup>®</sup> Tank designed for outdoor use with a four-layer design that resists UV rays, rust, and corrosion.</li>
	<li><strong>Worry-Free Maintenance</strong>An automatic Service Notification to your local Culligan Water Expert takes the hassle and guesswork out of maintenance.</li>
</ul>
</div>',
                'intro' => '<p>The Culligan High Efficiency Water Softener brings you better, softer water and can help prolong the lifespan and efficiency of your appliances, heating and plumbing systems.</p>

<p>The HE Softener features Aqua-Sensors® and smart meter components and automatically adjusts to changes in your home’s water conditions. It only regenerates when needed, and gives you the chance to control and customize water softness throughout your house.</p>',
                'slug' => 'bep-dien-tu-nnn',
                'manufacturer_id' => 1,
                'origin_id' => 2,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 9,
                'name' => 'Bếp điện từ RRR',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'price' => 565000,
                'cate_id' => 2,
                'body'=>'<div><strong>The High Efficiency 1-Inch Water Softener is designed to provide:</strong>
<ul>
	<li>A smart brine tank automatically monitors salt levels and tells you how many days of salt are remaining. Plus, an easy-to-read display provides system alerts and lets you know your softener is working.</li>
	<li><strong>Convenience</strong>The automatic bypass valve provides the convenience of skipping the water softener or the remote display</li>
	<li><strong>Reliability</strong>A specialized, non-corrosive valve provides more reliability than rotary valve systems. Culligan also offers a high-impact Quadra Hull<sup>®</sup> Tank designed for outdoor use with a four-layer design that resists UV rays, rust, and corrosion.</li>
	<li><strong>Worry-Free Maintenance</strong>An automatic Service Notification to your local Culligan Water Expert takes the hassle and guesswork out of maintenance.</li>
</ul>
</div>',
                'intro' => '<p>The Culligan High Efficiency Water Softener brings you better, softer water and can help prolong the lifespan and efficiency of your appliances, heating and plumbing systems.</p>

<p>The HE Softener features Aqua-Sensors® and smart meter components and automatically adjusts to changes in your home’s water conditions. It only regenerates when needed, and gives you the chance to control and customize water softness throughout your house.</p>',
                'slug' => 'bep-dien-tu-rrr',
                'manufacturer_id' => 1,
                'origin_id' => 2,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]
        ]);

        $products = \App\Model\Admin\Product::query()->whereIn('id', range(1,9))->get();

        foreach ($products as $product) {
          $product_gallery = \App\Model\Admin\ProductGallery::query()->create([
              'product_id' => $product->id,
              'sort' => 0
          ]);
          \App\Model\Common\File::query()->create([
              'path' => '\uploads\product_gallery\01jpg-1640584417-vslfjpg-1640860583-QNZ1.jpg',
              'model_id' => $product_gallery->id,
              'model_type' => 'App\Model\Admin\ProductGallery',
              'name' => '01jpg-1640584417-VSlf.jpg'
          ]);
            \App\Model\Common\File::query()->create([
                'path' => '\uploads\products\01jpg-1640584417-vslf-1jpg-1640860583-B2Ta.jpg',
                'custom_field' => 'image',
                'model_id' => $product->id,
                'model_type' => 'App\Model\Admin\Product',
                'name' => '01jpg-1640584417-VSlf.jpg'
            ]);
        };
    }
}
