<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ResourceType;
use App\Models\Recipe;
use App\Models\RecipeInput;
use App\Models\RecipeUnlockRequirement;

class GameDataSeeder extends Seeder
{
    public function run(): void
    {
        // TIER 0 - Elementos Primordiales
        $fuego = ResourceType::create([
            'name' => 'Fuego',
            'tier' => 0,
            'passive_generation_base' => 1.0,
            'icon' => '🔥',
            'sort_order' => 1
        ]);

        $agua = ResourceType::create([
            'name' => 'Agua',
            'tier' => 0,
            'passive_generation_base' => 1.0,
            'icon' => '💧',
            'sort_order' => 2
        ]);

        $aire = ResourceType::create([
            'name' => 'Aire',
            'tier' => 0,
            'passive_generation_base' => 1.0,
            'icon' => '💨',
            'sort_order' => 3
        ]);

        $tierra = ResourceType::create([
            'name' => 'Tierra',
            'tier' => 0,
            'passive_generation_base' => 1.0,
            'icon' => '🌍',
            'sort_order' => 4
        ]);

        // TIER 1 - Materiales Básicos
        $hierro = ResourceType::create([
            'name' => 'Hierro',
            'tier' => 1,
            'icon' => '⚙️',
            'sort_order' => 5
        ]);

        $cristal = ResourceType::create([
            'name' => 'Cristal',
            'tier' => 1,
            'icon' => '💎',
            'sort_order' => 6
        ]);

        $mercurio = ResourceType::create([
            'name' => 'Mercurio',
            'tier' => 1,
            'icon' => '🌊',
            'sort_order' => 7
        ]);

        $carbon = ResourceType::create([
            'name' => 'Carbón',
            'tier' => 1,
            'icon' => '⬛',
            'sort_order' => 8
        ]);

        // TIER 2 - Materiales Avanzados
        $mithril = ResourceType::create([
            'name' => 'Mithril',
            'tier' => 2,
            'icon' => '✨',
            'sort_order' => 9
        ]);

        $obsidiana = ResourceType::create([
            'name' => 'Obsidiana',
            'tier' => 2,
            'icon' => '🖤',
            'sort_order' => 10
        ]);

        $plataLunar = ResourceType::create([
            'name' => 'Plata Lunar',
            'tier' => 2,
            'icon' => '🌙',
            'sort_order' => 11
        ]);

        $acero = ResourceType::create([
            'name' => 'Acero',
            'tier' => 2,
            'icon' => '🔩',
            'sort_order' => 12
        ]);

        // TIER 3 - Materiales Superiores
        $adamantita = ResourceType::create([
            'name' => 'Adamantita',
            'tier' => 3,
            'icon' => '💠',
            'sort_order' => 13
        ]);

        $oricalco = ResourceType::create([
            'name' => 'Oricalco',
            'tier' => 3,
            'icon' => '🔱',
            'sort_order' => 14
        ]);

        $aleacionEterna = ResourceType::create([
            'name' => 'Aleación Eterna',
            'tier' => 3,
            'icon' => '⭐',
            'sort_order' => 15
        ]);

        // ===== RECETAS =====

        // TIER 1 Recipes
        $recetaHierro = Recipe::create([
            'name' => 'Forjar Hierro',
            'output_resource_type_id' => $hierro->id,
            'output_quantity' => 1,
            'is_unlocked' => false,
            'sort_order' => 1
        ]);
        RecipeInput::create(['recipe_id' => $recetaHierro->id, 'resource_type_id' => $tierra->id, 'quantity_required' => 10]);
        RecipeInput::create(['recipe_id' => $recetaHierro->id, 'resource_type_id' => $fuego->id, 'quantity_required' => 10]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaHierro->id, 'resource_type_id' => $fuego->id, 'quantity_required' => 50]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaHierro->id, 'resource_type_id' => $tierra->id, 'quantity_required' => 50]);

        $recetaCristal = Recipe::create([
            'name' => 'Crear Cristal',
            'output_resource_type_id' => $cristal->id,
            'output_quantity' => 1,
            'is_unlocked' => false,
            'sort_order' => 2
        ]);
        RecipeInput::create(['recipe_id' => $recetaCristal->id, 'resource_type_id' => $aire->id, 'quantity_required' => 10]);
        RecipeInput::create(['recipe_id' => $recetaCristal->id, 'resource_type_id' => $fuego->id, 'quantity_required' => 10]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaCristal->id, 'resource_type_id' => $fuego->id, 'quantity_required' => 50]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaCristal->id, 'resource_type_id' => $aire->id, 'quantity_required' => 50]);

        $recetaMercurio = Recipe::create([
            'name' => 'Destilar Mercurio',
            'output_resource_type_id' => $mercurio->id,
            'output_quantity' => 1,
            'is_unlocked' => false,
            'sort_order' => 3
        ]);
        RecipeInput::create(['recipe_id' => $recetaMercurio->id, 'resource_type_id' => $agua->id, 'quantity_required' => 10]);
        RecipeInput::create(['recipe_id' => $recetaMercurio->id, 'resource_type_id' => $aire->id, 'quantity_required' => 10]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaMercurio->id, 'resource_type_id' => $agua->id, 'quantity_required' => 50]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaMercurio->id, 'resource_type_id' => $aire->id, 'quantity_required' => 50]);

        $recetaCarbon = Recipe::create([
            'name' => 'Quemar Carbón',
            'output_resource_type_id' => $carbon->id,
            'output_quantity' => 1,
            'is_unlocked' => false,
            'sort_order' => 4
        ]);
        RecipeInput::create(['recipe_id' => $recetaCarbon->id, 'resource_type_id' => $tierra->id, 'quantity_required' => 10]);
        RecipeInput::create(['recipe_id' => $recetaCarbon->id, 'resource_type_id' => $agua->id, 'quantity_required' => 10]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaCarbon->id, 'resource_type_id' => $tierra->id, 'quantity_required' => 50]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaCarbon->id, 'resource_type_id' => $agua->id, 'quantity_required' => 50]);

        // TIER 2 Recipes
        $recetaMithril = Recipe::create([
            'name' => 'Fundir Mithril',
            'output_resource_type_id' => $mithril->id,
            'output_quantity' => 1,
            'is_unlocked' => false,
            'sort_order' => 5
        ]);
        RecipeInput::create(['recipe_id' => $recetaMithril->id, 'resource_type_id' => $hierro->id, 'quantity_required' => 10]);
        RecipeInput::create(['recipe_id' => $recetaMithril->id, 'resource_type_id' => $cristal->id, 'quantity_required' => 10]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaMithril->id, 'resource_type_id' => $hierro->id, 'quantity_required' => 100]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaMithril->id, 'resource_type_id' => $cristal->id, 'quantity_required' => 100]);

        $recetaObsidiana = Recipe::create([
            'name' => 'Forjar Obsidiana',
            'output_resource_type_id' => $obsidiana->id,
            'output_quantity' => 1,
            'is_unlocked' => false,
            'sort_order' => 6
        ]);
        RecipeInput::create(['recipe_id' => $recetaObsidiana->id, 'resource_type_id' => $carbon->id, 'quantity_required' => 10]);
        RecipeInput::create(['recipe_id' => $recetaObsidiana->id, 'resource_type_id' => $mercurio->id, 'quantity_required' => 10]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaObsidiana->id, 'resource_type_id' => $carbon->id, 'quantity_required' => 100]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaObsidiana->id, 'resource_type_id' => $mercurio->id, 'quantity_required' => 100]);

        $recetaPlataLunar = Recipe::create([
            'name' => 'Purificar Plata Lunar',
            'output_resource_type_id' => $plataLunar->id,
            'output_quantity' => 1,
            'is_unlocked' => false,
            'sort_order' => 7
        ]);
        RecipeInput::create(['recipe_id' => $recetaPlataLunar->id, 'resource_type_id' => $cristal->id, 'quantity_required' => 10]);
        RecipeInput::create(['recipe_id' => $recetaPlataLunar->id, 'resource_type_id' => $mercurio->id, 'quantity_required' => 10]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaPlataLunar->id, 'resource_type_id' => $mercurio->id, 'quantity_required' => 100]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaPlataLunar->id, 'resource_type_id' => $cristal->id, 'quantity_required' => 100]);

        $recetaAcero = Recipe::create([
            'name' => 'Templar Acero',
            'output_resource_type_id' => $acero->id,
            'output_quantity' => 1,
            'is_unlocked' => false,
            'sort_order' => 8
        ]);
        RecipeInput::create(['recipe_id' => $recetaAcero->id, 'resource_type_id' => $hierro->id, 'quantity_required' => 10]);
        RecipeInput::create(['recipe_id' => $recetaAcero->id, 'resource_type_id' => $carbon->id, 'quantity_required' => 10]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaAcero->id, 'resource_type_id' => $hierro->id, 'quantity_required' => 100]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaAcero->id, 'resource_type_id' => $carbon->id, 'quantity_required' => 100]);

        // TIER 3 Recipes
        $recetaAdamantita = Recipe::create([
            'name' => 'Crear Adamantita',
            'output_resource_type_id' => $adamantita->id,
            'output_quantity' => 1,
            'is_unlocked' => false,
            'sort_order' => 9
        ]);
        RecipeInput::create(['recipe_id' => $recetaAdamantita->id, 'resource_type_id' => $mithril->id, 'quantity_required' => 10]);
        RecipeInput::create(['recipe_id' => $recetaAdamantita->id, 'resource_type_id' => $obsidiana->id, 'quantity_required' => 10]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaAdamantita->id, 'resource_type_id' => $mithril->id, 'quantity_required' => 200]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaAdamantita->id, 'resource_type_id' => $obsidiana->id, 'quantity_required' => 200]);

        $recetaOricalco = Recipe::create([
            'name' => 'Alejar Oricalco',
            'output_resource_type_id' => $oricalco->id,
            'output_quantity' => 1,
            'is_unlocked' => false,
            'sort_order' => 10
        ]);
        RecipeInput::create(['recipe_id' => $recetaOricalco->id, 'resource_type_id' => $plataLunar->id, 'quantity_required' => 10]);
        RecipeInput::create(['recipe_id' => $recetaOricalco->id, 'resource_type_id' => $acero->id, 'quantity_required' => 10]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaOricalco->id, 'resource_type_id' => $plataLunar->id, 'quantity_required' => 200]);
        RecipeUnlockRequirement::create(['recipe_id' => $recetaOricalco->id, 'resource_type_id' => $acero->id, 'quantity_required' => 200]);

    }
}