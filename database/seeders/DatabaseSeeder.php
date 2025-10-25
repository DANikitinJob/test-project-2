<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use App\Models\OrganizationPhone;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Создаем здания
        Building::factory(5)->create();

        // Создаем организации
        Organization::factory(10)
            ->has(OrganizationPhone::factory()->count(2)) // Для каждой организации создаем 2 телефона
            ->create();

        // Создаем и связываем активности
        $food = Activity::create(['name' => 'Еда']);
        $cars = Activity::create(['name' => 'Автомобили']);

        $foodSubcategories = [
            'Мясная продукция',
            'Молочная продукция'
        ];

        $carsSubcategories = [
            'Грузовые' => [],
            'Легковые' => [
                'Запчасти',
                'Аксессуары'
            ]
        ];

        // Создаем подкатегории для еды
        foreach ($foodSubcategories as $subcategory) {
            Activity::create([
                'name' => $subcategory,
                'parent_id' => $food->id
            ]);
        }

        // Создаем подкатегории для автомобилей
        foreach ($carsSubcategories as $category => $subcategories) {
            $parentCategory = Activity::create([
                'name' => $category,
                'parent_id' => $cars->id
            ]);

            foreach ($subcategories as $subcategory) {
                Activity::create([
                    'name' => $subcategory,
                    'parent_id' => $parentCategory->id
                ]);
            }
        }

        // Связываем организации с случайными активностями
        Organization::all()->each(function ($organization) {
            $activities = Activity::inRandomOrder()->limit(rand(2, 3))->get();
            $organization->activities()->attach($activities);
        });
    }
}
