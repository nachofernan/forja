<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ResourceType;
use App\Models\Upgrade;
use App\Models\UpgradeCost;

class UpgradeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtener IDs de recursos
        $resources = ResourceType::whereIn('name', [
            'Fuego', 'Agua', 'Aire', 'Tierra', 'Hierro', 'Cristal', 
            'Mercurio', 'Carbón', 'Mithril', 'Acero', 'Obsidiana', 'Plata Lunar'
        ])->get()->keyBy('name');

        $fuego = $resources['Fuego'];
        $agua = $resources['Agua'];
        $aire = $resources['Aire'];
        $tierra = $resources['Tierra'];
        $hierro = $resources['Hierro'];
        $cristal = $resources['Cristal'];
        $mercurio = $resources['Mercurio'];
        $carbon = $resources['Carbón'];
        $mithril = $resources['Mithril'];
        $acero = $resources['Acero'];
        $obsidiana = $resources['Obsidiana'];
        $plataLunar = $resources['Plata Lunar'];

        // ========== CATEGORÍA: GENERACIÓN PASIVA ELEMENTAL (TIER 0) ==========

        // --- FUEGO ---
        $f1 = Upgrade::create(['code' => 'fuego_gen_boost_1', 'name' => 'Llama Eterna I', 'description' => 'Aumenta la generación pasiva de Fuego x2', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_resource_type_id' => $fuego->id, 'effect_value' => 2.0, 'sort_order' => 1]);
        UpgradeCost::create(['upgrade_id' => $f1->id, 'resource_type_id' => $fuego->id, 'quantity_required' => 100]);

        $f2 = Upgrade::create(['code' => 'hierro_efficiency_1', 'name' => 'Forja Maestra: Hierro', 'description' => 'Reduce el costo de craftear Hierro en 20%', 'category' => 'passive_generation', 'effect_type' => 'reduce_recipe_cost', 'target_resource_type_id' => $hierro->id, 'effect_value' => 0.20, 'requires_upgrade_id' => $f1->id,  'sort_order' => 2]);
        UpgradeCost::create(['upgrade_id' => $f2->id, 'resource_type_id' => $hierro->id, 'quantity_required' => 50]);
        UpgradeCost::create(['upgrade_id' => $f2->id, 'resource_type_id' => $carbon->id, 'quantity_required' => 50]);

        $f3 = Upgrade::create(['code' => 'fuego_gen_boost_2', 'name' => 'Llama Eterna II', 'description' => 'Aumenta la generación pasiva de Fuego x2', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_resource_type_id' => $fuego->id, 'effect_value' => 2.0, 'requires_upgrade_id' => $f2->id, 'sort_order' => 3]);
        UpgradeCost::create(['upgrade_id' => $f3->id, 'resource_type_id' => $fuego->id, 'quantity_required' => 2000]);
        UpgradeCost::create(['upgrade_id' => $f3->id, 'resource_type_id' => $carbon->id, 'quantity_required' => 100]);

        $f4 = Upgrade::create(['code' => 'obsidiana_efficiency_1', 'name' => 'Forja Volcánica', 'description' => 'Reduce el costo de craftear Obsidiana en 20%', 'category' => 'passive_generation', 'effect_type' => 'reduce_recipe_cost', 'target_resource_type_id' => $obsidiana->id, 'effect_value' => 0.20, 'requires_upgrade_id' => $f3->id, 'sort_order' => 4]);
        UpgradeCost::create(['upgrade_id' => $f4->id, 'resource_type_id' => $obsidiana->id, 'quantity_required' => 100]);
        UpgradeCost::create(['upgrade_id' => $f4->id, 'resource_type_id' => $carbon->id, 'quantity_required' => 200]);

        $f5 = Upgrade::create(['code' => 'fuego_gen_boost_3', 'name' => 'Llama Eterna III', 'description' => 'Aumenta la generación pasiva de Fuego x2', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_resource_type_id' => $fuego->id, 'effect_value' => 2.0, 'requires_upgrade_id' => $f4->id, 'sort_order' => 5]);
        UpgradeCost::create(['upgrade_id' => $f5->id, 'resource_type_id' => $fuego->id, 'quantity_required' => 4000]);
        UpgradeCost::create(['upgrade_id' => $f5->id, 'resource_type_id' => $mithril->id, 'quantity_required' => 200]);

        // --- AGUA ---
        $a1 = Upgrade::create(['code' => 'agua_gen_boost_1', 'name' => 'Manantial Perpetuo I', 'description' => 'Aumenta la generación pasiva de Agua x2', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_resource_type_id' => $agua->id, 'effect_value' => 2.0, 'sort_order' => 6]);
        UpgradeCost::create(['upgrade_id' => $a1->id, 'resource_type_id' => $agua->id, 'quantity_required' => 100]);

        $a2 = Upgrade::create(['code' => 'cristal_efficiency_1', 'name' => 'Maestría Cristalina', 'description' => 'Reduce el costo de craftear Cristal en 20%', 'category' => 'passive_generation', 'effect_type' => 'reduce_recipe_cost', 'target_resource_type_id' => $cristal->id, 'effect_value' => 0.20, 'requires_upgrade_id' => $a1->id, 'sort_order' => 7]);
        UpgradeCost::create(['upgrade_id' => $a2->id, 'resource_type_id' => $cristal->id, 'quantity_required' => 50]);
        UpgradeCost::create(['upgrade_id' => $a2->id, 'resource_type_id' => $hierro->id, 'quantity_required' => 50]);

        $a3 = Upgrade::create(['code' => 'agua_gen_boost_2', 'name' => 'Manantial Perpetuo II', 'description' => 'Aumenta la generación pasiva de Agua x2', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_resource_type_id' => $agua->id, 'effect_value' => 2.0, 'requires_upgrade_id' => $a2->id, 'sort_order' => 8]);
        UpgradeCost::create(['upgrade_id' => $a3->id, 'resource_type_id' => $agua->id, 'quantity_required' => 2000]);
        UpgradeCost::create(['upgrade_id' => $a3->id, 'resource_type_id' => $mercurio->id, 'quantity_required' => 100]);

        $a4 = Upgrade::create(['code' => 'acero_efficiency_1', 'name' => 'Templado Perfecto', 'description' => 'Reduce el costo de craftear Acero en 20%', 'category' => 'passive_generation', 'effect_type' => 'reduce_recipe_cost', 'target_resource_type_id' => $acero->id, 'effect_value' => 0.20,'requires_upgrade_id' => $a3->id, 'sort_order' => 9]);
        UpgradeCost::create(['upgrade_id' => $a4->id, 'resource_type_id' => $acero->id, 'quantity_required' => 100]);
        UpgradeCost::create(['upgrade_id' => $a4->id, 'resource_type_id' => $hierro->id, 'quantity_required' => 200]);

        $a5 = Upgrade::create(['code' => 'agua_gen_boost_3', 'name' => 'Manantial Perpetuo III', 'description' => 'Aumenta la generación pasiva de Agua x2', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_resource_type_id' => $agua->id, 'effect_value' => 2.0, 'requires_upgrade_id' => $a4->id, 'sort_order' => 10]);
        UpgradeCost::create(['upgrade_id' => $a5->id, 'resource_type_id' => $agua->id, 'quantity_required' => 4000]);
        UpgradeCost::create(['upgrade_id' => $a5->id, 'resource_type_id' => $plataLunar->id, 'quantity_required' => 200]);

        // --- AIRE ---
        $ai1 = Upgrade::create(['code' => 'aire_gen_boost_1', 'name' => 'Vendaval Eterno I', 'description' => 'Aumenta la generación pasiva de Aire x2', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_resource_type_id' => $aire->id, 'effect_value' => 2.0, 'sort_order' => 11]);
        UpgradeCost::create(['upgrade_id' => $ai1->id, 'resource_type_id' => $aire->id, 'quantity_required' => 100]);

        $ai2 = Upgrade::create(['code' => 'mercurio_efficiency_1', 'name' => 'Alquimia Avanzada', 'description' => 'Reduce el costo de craftear Mercurio en 20%', 'category' => 'passive_generation', 'effect_type' => 'reduce_recipe_cost', 'target_resource_type_id' => $mercurio->id, 'effect_value' => 0.20, 'requires_upgrade_id' => $ai1->id, 'sort_order' => 12]);
        UpgradeCost::create(['upgrade_id' => $ai2->id, 'resource_type_id' => $mercurio->id, 'quantity_required' => 50]);
        UpgradeCost::create(['upgrade_id' => $ai2->id, 'resource_type_id' => $cristal->id, 'quantity_required' => 50]);

        $ai3 = Upgrade::create(['code' => 'aire_gen_boost_2', 'name' => 'Vendaval Eterno II', 'description' => 'Aumenta la generación pasiva de Aire x2', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_resource_type_id' => $aire->id, 'effect_value' => 2.0, 'requires_upgrade_id' => $ai2->id, 'sort_order' => 13]);
        UpgradeCost::create(['upgrade_id' => $ai3->id, 'resource_type_id' => $aire->id, 'quantity_required' => 2000]);
        UpgradeCost::create(['upgrade_id' => $ai3->id, 'resource_type_id' => $hierro->id, 'quantity_required' => 100]);

        $ai4 = Upgrade::create(['code' => 'mithril_efficiency_1', 'name' => 'Secretos del Mithril', 'description' => 'Reduce el costo de craftear Mithril en 20%', 'category' => 'passive_generation', 'effect_type' => 'reduce_recipe_cost', 'target_resource_type_id' => $mithril->id, 'effect_value' => 0.20, 'requires_upgrade_id' => $ai3->id, 'sort_order' => 14]);
        UpgradeCost::create(['upgrade_id' => $ai4->id, 'resource_type_id' => $mithril->id, 'quantity_required' => 100]);
        UpgradeCost::create(['upgrade_id' => $ai4->id, 'resource_type_id' => $cristal->id, 'quantity_required' => 200]);

        $ai5 = Upgrade::create(['code' => 'aire_gen_boost_3', 'name' => 'Vendaval Eterno III', 'description' => 'Aumenta la generación pasiva de Aire x2', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_resource_type_id' => $aire->id, 'effect_value' => 2.0, 'requires_upgrade_id' => $ai4->id, 'sort_order' => 15]);
        UpgradeCost::create(['upgrade_id' => $ai5->id, 'resource_type_id' => $aire->id, 'quantity_required' => 4000]);
        UpgradeCost::create(['upgrade_id' => $ai5->id, 'resource_type_id' => $obsidiana->id, 'quantity_required' => 200]);

        // --- TIERRA ---
        $t1 = Upgrade::create(['code' => 'tierra_gen_boost_1', 'name' => 'Corazón de Roca I', 'description' => 'Aumenta la generación pasiva de Tierra x2', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_resource_type_id' => $tierra->id, 'effect_value' => 2.0, 'sort_order' => 16]);
        UpgradeCost::create(['upgrade_id' => $t1->id, 'resource_type_id' => $tierra->id, 'quantity_required' => 100]);

        $t2 = Upgrade::create(['code' => 'carbon_efficiency_1', 'name' => 'Combustión Perfecta', 'description' => 'Reduce el costo de craftear Carbón en 20%', 'category' => 'passive_generation', 'effect_type' => 'reduce_recipe_cost', 'target_resource_type_id' => $carbon->id, 'effect_value' => 0.20, 'requires_upgrade_id' => $t1->id, 'sort_order' => 17]);
        UpgradeCost::create(['upgrade_id' => $t2->id, 'resource_type_id' => $carbon->id, 'quantity_required' => 50]);
        UpgradeCost::create(['upgrade_id' => $t2->id, 'resource_type_id' => $mercurio->id, 'quantity_required' => 50]);

        $t3 = Upgrade::create(['code' => 'tierra_gen_boost_2', 'name' => 'Corazón de Roca II', 'description' => 'Aumenta la generación pasiva de Tierra x2', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_resource_type_id' => $tierra->id, 'effect_value' => 2.0, 'requires_upgrade_id' => $t2->id, 'sort_order' => 18]);
        UpgradeCost::create(['upgrade_id' => $t3->id, 'resource_type_id' => $tierra->id, 'quantity_required' => 2000]);
        UpgradeCost::create(['upgrade_id' => $t3->id, 'resource_type_id' => $cristal->id, 'quantity_required' => 100]);

        $t4 = Upgrade::create(['code' => 'plata_lunar_efficiency_1', 'name' => 'Purificación Lunar', 'description' => 'Reduce el costo de craftear Plata Lunar en 20%', 'category' => 'passive_generation', 'effect_type' => 'reduce_recipe_cost', 'target_resource_type_id' => $plataLunar->id, 'effect_value' => 0.20, 'requires_upgrade_id' => $t3->id, 'sort_order' => 19]);
        UpgradeCost::create(['upgrade_id' => $t4->id, 'resource_type_id' => $plataLunar->id, 'quantity_required' => 100]);
        UpgradeCost::create(['upgrade_id' => $t4->id, 'resource_type_id' => $mercurio->id, 'quantity_required' => 200]);

        $t5 = Upgrade::create(['code' => 'tierra_gen_boost_3', 'name' => 'Corazón de Roca III', 'description' => 'Aumenta la generación pasiva de Tierra x2', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_resource_type_id' => $tierra->id, 'effect_value' => 2.0, 'requires_upgrade_id' => $t4->id, 'sort_order' => 20]);
        UpgradeCost::create(['upgrade_id' => $t5->id, 'resource_type_id' => $tierra->id, 'quantity_required' => 4000]);
        UpgradeCost::create(['upgrade_id' => $t5->id, 'resource_type_id' => $acero->id, 'quantity_required' => 200]);


        // ========== ÁRBOL DE ARMONÍA ELEMENTAL (GLOBAL TIER 0) ==========

        $arm1 = Upgrade::create(['code' => 'all_elements_boost_1', 'name' => 'Armonía Elemental I', 'description' => 'Aumenta la generación de TODOS los elementos x1.5', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_tier' => 0, 'effect_value' => 1.5, 'sort_order' => 21]);
        UpgradeCost::create(['upgrade_id' => $arm1->id, 'resource_type_id' => $fuego->id, 'quantity_required' => 1000]);
        UpgradeCost::create(['upgrade_id' => $arm1->id, 'resource_type_id' => $agua->id, 'quantity_required' => 1000]);
        UpgradeCost::create(['upgrade_id' => $arm1->id, 'resource_type_id' => $aire->id, 'quantity_required' => 1000]);
        UpgradeCost::create(['upgrade_id' => $arm1->id, 'resource_type_id' => $tierra->id, 'quantity_required' => 1000]);

        $tier1GlobalEff = Upgrade::create(['code' => 'tier1_global_efficiency', 'name' => 'Artesanía Magistral', 'description' => 'Reduce el costo de TODAS las recetas Tier 1 en 15%', 'category' => 'passive_generation', 'effect_type' => 'reduce_recipe_cost', 'target_tier' => 1, 'effect_value' => 0.15, 'requires_upgrade_id' => $arm1->id, 'sort_order' => 22]);
        UpgradeCost::create(['upgrade_id' => $tier1GlobalEff->id, 'resource_type_id' => $hierro->id, 'quantity_required' => 100]);
        UpgradeCost::create(['upgrade_id' => $tier1GlobalEff->id, 'resource_type_id' => $cristal->id, 'quantity_required' => 100]);
        UpgradeCost::create(['upgrade_id' => $tier1GlobalEff->id, 'resource_type_id' => $mercurio->id, 'quantity_required' => 100]);
        UpgradeCost::create(['upgrade_id' => $tier1GlobalEff->id, 'resource_type_id' => $carbon->id, 'quantity_required' => 100]);

        $arm2 = Upgrade::create(['code' => 'all_elements_boost_2', 'name' => 'Armonía Elemental II', 'description' => 'Aumenta la generación de TODOS los elementos x1.5', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_tier' => 0, 'effect_value' => 1.5, 'requires_upgrade_id' => $tier1GlobalEff->id, 'sort_order' => 23]);
        UpgradeCost::create(['upgrade_id' => $arm2->id, 'resource_type_id' => $fuego->id, 'quantity_required' => 3000]);
        UpgradeCost::create(['upgrade_id' => $arm2->id, 'resource_type_id' => $agua->id, 'quantity_required' => 3000]);
        UpgradeCost::create(['upgrade_id' => $arm2->id, 'resource_type_id' => $aire->id, 'quantity_required' => 3000]);
        UpgradeCost::create(['upgrade_id' => $arm2->id, 'resource_type_id' => $tierra->id, 'quantity_required' => 3000]);
        
        $tier2GlobalEff = Upgrade::create(['code' => 'tier2_global_efficiency', 'name' => 'Maestría Avanzada', 'description' => 'Reduce el costo de TODAS las recetas Tier 2 en 15%', 'category' => 'passive_generation', 'effect_type' => 'reduce_recipe_cost', 'target_tier' => 2, 'effect_value' => 0.15, 'requires_upgrade_id' => $arm2->id, 'sort_order' => 24]);
        UpgradeCost::create(['upgrade_id' => $tier2GlobalEff->id, 'resource_type_id' => $mithril->id, 'quantity_required' => 150]);
        UpgradeCost::create(['upgrade_id' => $tier2GlobalEff->id, 'resource_type_id' => $obsidiana->id, 'quantity_required' => 150]);
        UpgradeCost::create(['upgrade_id' => $tier2GlobalEff->id, 'resource_type_id' => $plataLunar->id, 'quantity_required' => 150]);
        UpgradeCost::create(['upgrade_id' => $tier2GlobalEff->id, 'resource_type_id' => $acero->id, 'quantity_required' => 150]);

        $arm3 = Upgrade::create(['code' => 'all_elements_boost_3', 'name' => 'Armonía Elemental III', 'description' => 'Aumenta la generación de TODOS los elementos x1.5', 'category' => 'passive_generation', 'effect_type' => 'multiply_generation', 'target_tier' => 0, 'effect_value' => 1.5, 'requires_upgrade_id' => $tier2GlobalEff->id, 'sort_order' => 25]);
        UpgradeCost::create(['upgrade_id' => $arm3->id, 'resource_type_id' => $fuego->id, 'quantity_required' => 10000]);
        UpgradeCost::create(['upgrade_id' => $arm3->id, 'resource_type_id' => $agua->id, 'quantity_required' => 10000]);
        UpgradeCost::create(['upgrade_id' => $arm3->id, 'resource_type_id' => $aire->id, 'quantity_required' => 10000]);
        UpgradeCost::create(['upgrade_id' => $arm3->id, 'resource_type_id' => $tierra->id, 'quantity_required' => 10000]);


        // ========== CATEGORÍA: TURNOS Y TIEMPO ==========

        $turns1 = Upgrade::create(['code' => 'add_turns_1', 'name' => 'Extensión Temporal I', 'description' => 'Añade +5 turnos por día', 'category' => 'turns', 'effect_type' => 'add_turns', 'effect_value' => 5, 'sort_order' => 26]);
        UpgradeCost::create(['upgrade_id' => $turns1->id, 'resource_type_id' => $fuego->id, 'quantity_required' => 100]);
        UpgradeCost::create(['upgrade_id' => $turns1->id, 'resource_type_id' => $agua->id, 'quantity_required' => 100]);

        $turns2 = Upgrade::create(['code' => 'add_turns_2', 'name' => 'Extensión Temporal II', 'description' => 'Añade +10 turnos por día', 'category' => 'turns', 'effect_type' => 'add_turns', 'effect_value' => 10, 'requires_upgrade_id' => $turns1->id, 'sort_order' => 27]);
        UpgradeCost::create(['upgrade_id' => $turns2->id, 'resource_type_id' => $tierra->id, 'quantity_required' => 150]);
        UpgradeCost::create(['upgrade_id' => $turns2->id, 'resource_type_id' => $aire->id, 'quantity_required' => 150]);

        $turns3 = Upgrade::create(['code' => 'add_turns_3', 'name' => 'Extensión Temporal III', 'description' => 'Añade +15 turnos por día', 'category' => 'turns', 'effect_type' => 'add_turns', 'effect_value' => 15, 'requires_upgrade_id' => $turns2->id, 'sort_order' => 28]);
        UpgradeCost::create(['upgrade_id' => $turns3->id, 'resource_type_id' => $hierro->id, 'quantity_required' => 100]);
        UpgradeCost::create(['upgrade_id' => $turns3->id, 'resource_type_id' => $carbon->id, 'quantity_required' => 100]);

        $turns4 = Upgrade::create(['code' => 'add_turns_4', 'name' => 'Extensión Temporal IV', 'description' => 'Añade +20 turnos por día', 'category' => 'turns', 'effect_type' => 'add_turns', 'effect_value' => 20, 'requires_upgrade_id' => $turns3->id, 'sort_order' => 29]);
        UpgradeCost::create(['upgrade_id' => $turns4->id, 'resource_type_id' => $mercurio->id, 'quantity_required' => 150]);
        UpgradeCost::create(['upgrade_id' => $turns4->id, 'resource_type_id' => $cristal->id, 'quantity_required' => 150]);

        $turns5 = Upgrade::create(['code' => 'add_turns_5', 'name' => 'Extensión Temporal V', 'description' => 'Añade +25 turnos por día', 'category' => 'turns', 'effect_type' => 'add_turns', 'effect_value' => 25, 'requires_upgrade_id' => $turns4->id, 'sort_order' => 30]);
        UpgradeCost::create(['upgrade_id' => $turns5->id, 'resource_type_id' => $mithril->id, 'quantity_required' => 200]);
        UpgradeCost::create(['upgrade_id' => $turns5->id, 'resource_type_id' => $acero->id, 'quantity_required' => 200]);
        UpgradeCost::create(['upgrade_id' => $turns5->id, 'resource_type_id' => $obsidiana->id, 'quantity_required' => 200]);
        UpgradeCost::create(['upgrade_id' => $turns5->id, 'resource_type_id' => $plataLunar->id, 'quantity_required' => 200]);

        


        
    }
}