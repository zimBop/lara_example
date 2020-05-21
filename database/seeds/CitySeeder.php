<?php

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * @see https://nominatim.openstreetmap.org/ - to find relation (e.g. Hartford, CT, US)
     * @see http://polygons.openstreetmap.fr/ - to create polygon by relation ID
     *
     * @return void
     */
    public function run()
    {
        City::firstOrCreate(
            [City::NAME => 'Hartford'],
            [
                City::POLYGON => DB::raw("ST_GeometryFromText('POLYGON((-72.722 41.806,-72.722 41.808,-72.719 41.811,-72.689 41.811,-72.687 41.807,-72.663 41.811,-72.643 41.809,-72.642 41.788,-72.644 41.782,-72.648 41.777,-72.661 41.77,-72.661 41.764,-72.653 41.756,-72.646 41.754,-72.641 41.749,-72.638 41.735,-72.643 41.726,-72.646 41.724,-72.656 41.726,-72.706 41.72,-72.718 41.721,-72.72 41.744,-72.72 41.773,-72.718 41.78,-72.722 41.806))', 4326)"),
                City::CENTER => DB::raw("ST_GeometryFromText('POINT(-72.6908547 41.764582)', 4326)"),
            ]
        );

        City::firstOrCreate(
            [City::NAME => 'New Haven'],
            [
                City::POLYGON => DB::raw("ST_GeometryFromText('POLYGON((-72.998 41.31,-73.002 41.312,-73 41.327,-72.995 41.329,-72.985 41.341,-72.976 41.345,-72.977 41.349,-72.972 41.352,-72.965 41.351,-72.96 41.354,-72.953 41.354,-72.949 41.351,-72.947 41.346,-72.948 41.341,-72.925 41.338,-72.913 41.338,-72.902 41.331,-72.896 41.332,-72.895 41.334,-72.889 41.337,-72.868 41.34,-72.859 41.34,-72.856 41.336,-72.868 41.308,-72.884 41.284,-72.883 41.252,-72.886 41.248,-72.897 41.245,-72.901 41.242,-72.908 41.246,-72.908 41.252,-72.905 41.257,-72.898 41.26,-72.91 41.269,-72.908 41.278,-72.912 41.282,-72.911 41.293,-72.913 41.293,-72.919 41.287,-72.924 41.279,-72.929 41.276,-72.934 41.276,-72.936 41.278,-72.941 41.278,-72.943 41.28,-72.944 41.286,-72.953 41.29,-72.956 41.293,-72.955 41.296,-72.96 41.3,-72.961 41.307,-72.998 41.31))', 4326)"),
                City::CENTER => DB::raw("ST_GeometryFromText('POINT(-72.9250518 41.3082138)', 4326)"),
            ]
        );
    }
}
