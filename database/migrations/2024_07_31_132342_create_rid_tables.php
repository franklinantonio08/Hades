<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMigrationTablesV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Eliminar tablas si ya existen
        Schema::dropIfExists('RID_migrante');
        Schema::dropIfExists('RID_afinidad');
        Schema::dropIfExists('RID_puestoControl');
        Schema::dropIfExists('RID_paises');
        Schema::dropIfExists('RID_regiones');
        Schema::dropIfExists('RID_ProcessLogs');

        // Crear tabla RID_regiones
        Schema::create('RID_regiones', function (Blueprint $table) {
            $table->id();
            $table->string('continente');
            $table->string('region');
            $table->enum('estatus', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
        });

        // Insertar datos en RID_regiones
        DB::table('RID_regiones')->insert([
            ['continente' => 'África', 'region' => 'África del Norte'],
            ['continente' => 'África', 'region' => 'África Subsahariana'],
            ['continente' => 'África', 'region' => 'África Occidental'],
            ['continente' => 'África', 'region' => 'África Central'],
            ['continente' => 'África', 'region' => 'África Oriental'],
            ['continente' => 'África', 'region' => 'África Meridional'],
            ['continente' => 'América', 'region' => 'América del Norte'],
            ['continente' => 'América', 'region' => 'América Central'],
            ['continente' => 'América', 'region' => 'Caribe'],
            ['continente' => 'América', 'region' => 'América del Sur'],
            ['continente' => 'Asia', 'region' => 'Asia Oriental'],
            ['continente' => 'Asia', 'region' => 'Asia Meridional'],
            ['continente' => 'Asia', 'region' => 'Sudeste Asiático'],
            ['continente' => 'Asia', 'region' => 'Asia Central'],
            ['continente' => 'Asia', 'region' => 'Asia Occidental'],
            ['continente' => 'Europa', 'region' => 'Europa Occidental'],
            ['continente' => 'Europa', 'region' => 'Europa Central'],
            ['continente' => 'Europa', 'region' => 'Europa Oriental'],
            ['continente' => 'Europa', 'region' => 'Europa del Norte'],
            ['continente' => 'Europa', 'region' => 'Europa del Sur'],
            ['continente' => 'Oceanía', 'region' => 'Australasia'],
            ['continente' => 'Oceanía', 'region' => 'Melanesia'],
            ['continente' => 'Oceanía', 'region' => 'Micronesia'],
            ['continente' => 'Oceanía', 'region' => 'Polinesia'],
            ['continente' => 'Antártida', 'region' => 'Antártida']
        ]);

        // Crear tabla RID_paises
        Schema::create('RID_paises', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('pais');
            $table->string('cod_pais', 3);
            $table->integer('region_id');
            $table->enum('estatus', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
            $table->foreign('region_id')->references('id')->on('RID_regiones');
        });

        // Insertar datos en RID_paises
        DB::table('RID_paises')->insert([
            [1, 'Afganistán', 'AFG', 12],
            [2, 'Alemania', 'DEU', 16],
            [3, 'Andorra', 'AND', 20],
            [4, 'Angola', 'AGO', 4],
            [5, 'Antigua y Barbuda', 'ATG', 9],
            [6, 'Arabia Saudita', 'SAU', 15],
            [7, 'Argelia', 'DZA', 1],
            [8, 'Argentina', 'ARG', 10],
            [9, 'Armenia', 'ARM', 15],
            [10, 'Australia', 'AUS', 21],
            [11, 'Austria', 'AUT', 16],
            [12, 'Azerbaiyán', 'AZE', 15],
            [13, 'Bahamas', 'BHS', 9],
            [14, 'Baréin', 'BHR', 15],
            [15, 'Bangladés', 'BGD', 12],
            [16, 'Barbados', 'BRB', 9],
            [17, 'Bélgica', 'BEL', 16],
            [18, 'Belice', 'BLZ', 8],
            [19, 'Benín', 'BEN', 3],
            [20, 'Bielorrusia', 'BLR', 18],
            [21, 'Bolivia', 'BOL', 10],
            [22, 'Bosnia y Herzegovina', 'BIH', 20],
            [23, 'Botsuana', 'BWA', 6],
            [24, 'Brasil', 'BRA', 10],
            [25, 'Brunéi', 'BRN', 13],
            [26, 'Bulgaria', 'BGR', 18],
            [27, 'Burkina Faso', 'BFA', 3],
            [28, 'Burundi', 'BDI', 5],
            [29, 'Bután', 'BTN', 12],
            [30, 'Cabo Verde', 'CPV', 3],
            [31, 'Camboya', 'KHM', 13],
            [32, 'Camerún', 'CMR', 4],
            [33, 'Canadá', 'CAN', 7],
            [34, 'Catar', 'QAT', 15],
            [35, 'Chad', 'TCD', 4],
            [36, 'Chile', 'CHL', 10],
            [37, 'China', 'CHN', 11],
            [38, 'Chipre', 'CYP', 15],
            [39, 'Colombia', 'COL', 10],
            [40, 'Comoras', 'COM', 5],
            [41, 'Congo, República del', 'COG', 4],
            [42, 'Congo, República Democrática del', 'COD', 4],
            [43, 'Corea del Norte', 'PRK', 11],
            [44, 'Corea del Sur', 'KOR', 11],
            [45, 'Costa de Marfil', 'CIV', 3],
            [46, 'Costa Rica', 'CRI', 8],
            [47, 'Croacia', 'HRV', 17],
            [48, 'Cuba', 'CUB', 9],
            [49, 'Dinamarca', 'DNK', 19],
            [50, 'Djibouti', 'DJI', 5],
            [51, 'Dominica', 'DMA', 9],
            [52, 'Ecuador', 'ECU', 10],
            [53, 'Egipto', 'EGY', 1],
            [54, 'El Salvador', 'SLV', 8],
            [55, 'Emiratos Árabes Unidos', 'ARE', 15],
            [56, 'Eritrea', 'ERI', 5],
            [57, 'Eslovaquia', 'SVK', 18],
            [58, 'Eslovenia', 'SVN', 17],
            [59, 'España', 'ESP', 20],
            [60, 'Estados Unidos', 'USA', 7],
            [61, 'Estonia', 'EST', 19],
            [62, 'Esuatini', 'SWZ', 6],
            [63, 'Etiopía', 'ETH', 5],
            [64, 'Filipinas', 'PHL', 13],
            [65, 'Finlandia', 'FIN', 19],
            [66, 'Fiyi', 'FJI', 22],
            [67, 'Francia', 'FRA', 16],
            [68, 'Gabón', 'GAB', 4],
            [69, 'Gambia', 'GMB', 3],
            [70, 'Georgia', 'GEO', 15],
            [71, 'Ghana', 'GHA', 3],
            [72, 'Granada', 'GRD', 9],
            [73, 'Grecia', 'GRC', 20],
            [74, 'Guatemala', 'GTM', 8],
            [75, 'Guinea', 'GIN', 3],
            [76, 'Guinea Ecuatorial', 'GNQ', 4],
            [77, 'Guinea-Bisáu', 'GNB', 3],
            [78, 'Guyana', 'GUY', 10],
            [79, 'Haití', 'HTI', 9],
            [80, 'Holanda', 'NLD', 16],
            [81, 'Honduras', 'HND', 8],
            [82, 'Hungría', 'HUN', 18],
            [83, 'India', 'IND', 12],
            [84, 'Indonesia', 'IDN', 13],
            [85, 'Irán', 'IRN', 15],
            [86, 'Iraq', 'IRQ', 15],
            [87, 'Irlanda', 'IRL', 19],
            [88, 'Islas Marshall', 'MHL', 22],
            [89, 'Islas Salomón', 'SLB', 22],
            [90, 'Islandia', 'ISL', 19],
            [91, 'Israel', 'ISR', 15],
            [92, 'Italia', 'ITA', 20],
            [93, 'Jamaica', 'JAM', 9],
            [94, 'Japón', 'JPN', 11],
            [95, 'Jordania', 'JOR', 15],
            [96, 'Kazajistán', 'KAZ', 15],
            [97, 'Kenia', 'KEN', 5],
            [98, 'Kirguistán', 'KGZ', 15],
            [99, 'Kiribati', 'KIR', 22],
            [100, 'Kuwait', 'KWT', 15],
            [101, 'Laos', 'LAO', 13],
            [102, 'Letonia', 'LVA', 19],
            [103, 'Líbano', 'LBN', 15],
            [104, 'Liberia', 'LBR', 3],
            [105, 'Libia', 'LBY', 1],
            [106, 'Liechtenstein', 'LIE', 16],
            [107, 'Lituania', 'LTU', 19],
            [108, 'Luxemburgo', 'LUX', 16],
            [109, 'Madagascar', 'MDG', 5],
            [110, 'Malasia', 'MYS', 13],
            [111, 'Malawi', 'MWI', 5],
            [112, 'Maldivas', 'MDV', 13],
            [113, 'Malí', 'MLI', 3],
            [114, 'Malta', 'MLT', 20],
            [115, 'Marruecos', 'MAR', 1],
            [116, 'Mauricio', 'MUS', 6],
            [117, 'Mauritania', 'MRT', 4],
            [118, 'México', 'MEX', 8],
            [119, 'Micronesia', 'FSM', 22],
            [120, 'Moldova', 'MDA', 18],
            [121, 'Mónaco', 'MCO', 20],
            [122, 'Mongolia', 'MNG', 15],
            [123, 'Montenegro', 'MNE', 20],
            [124, 'Moravia', 'MOR', 20],
            [125, 'Mozambique', 'MOZ', 5],
            [126, 'Namibia', 'NAM', 6],
            [127, 'Nauru', 'NRU', 22],
            [128, 'Nepal', 'NPL', 12],
            [129, 'Nicaragua', 'NIC', 8],
            [130, 'Níger', 'NER', 3],
            [131, 'Nigeria', 'NGA', 3],
            [132, 'Noruega', 'NOR', 19],
            [133, 'Nueva Zelanda', 'NZL', 21],
            [134, 'Omán', 'OMN', 15],
            [135, 'Países Bajos', 'NLD', 16],
            [136, 'Pakistán', 'PAK', 12],
            [137, 'Palaú', 'PLW', 22],
            [138, 'Panamá', 'PAN', 8],
            [139, 'Papúa Nueva Guinea', 'PNG', 22],
            [140, 'Paraguay', 'PRY', 10],
            [141, 'Perú', 'PER', 10],
            [142, 'Polonia', 'POL', 18],
            [143, 'Portugal', 'PRT', 20],
            [144, 'Qatar', 'QAT', 15],
            [145, 'Rumanía', 'ROU', 18],
            [146, 'Rusia', 'RUS', 18],
            [147, 'Rwanda', 'RWA', 5],
            [148, 'San Cristóbal y Nieves', 'KNA', 9],
            [149, 'San Marino', 'SMR', 20],
            [150, 'Santa Lucía', 'LCA', 9],
            [151, 'San Tomé y Príncipe', 'STP', 5],
            [152, 'Senegal', 'SEN', 3],
            [153, 'Serbia', 'SRB', 20],
            [154, 'Seychelles', 'SYC', 6],
            [155, 'Sierra Leona', 'SLE', 3],
            [156, 'Singapur', 'SGP', 13],
            [157, 'Siria', 'SYR', 15],
            [158, 'Somalia', 'SOM', 5],
            [159, 'Sri Lanka', 'LKA', 12],
            [160, 'Sudáfrica', 'ZAF', 6],
            [161, 'Sudán', 'SDN', 4],
            [162, 'Suecia', 'SWE', 19],
            [163, 'Suiza', 'CHE', 16],
            [164, 'Santo Tomé y Príncipe', 'STP', 5],
            [165, 'Tailandia', 'THA', 13],
            [166, 'Timor Oriental', 'TLS', 13],
            [167, 'Togo', 'TGO', 3],
            [168, 'Tonga', 'TON', 22],
            [169, 'Trinidad y Tobago', 'TTO', 9],
            [170, 'Túnez', 'TUN', 1],
            [171, 'Turkmenistán', 'TKM', 15],
            [172, 'Turquía', 'TUR', 15],
            [173, 'Tuvalu', 'TUV', 22],
            [174, 'Ucrania', 'UKR', 18],
            [175, 'Uganda', 'UGA', 5],
            [176, 'Uruguay', 'URY', 10],
            [177, 'Uzbekistán', 'UZB', 15],
            [178, 'Vanuatu', 'VUT', 22],
            [179, 'Vaticano', 'VAT', 20],
            [180, 'Venezuela', 'VEN', 10],
            [181, 'Vietnam', 'VNM', 13],
            [182, 'Yemen', 'YEM', 15],
            [183, 'Zambia', 'ZMB', 5],
            [184, 'Zimbabue', 'ZWE', 5],
        ]);

        // Crear tabla RID_puestoControl
        Schema::create('RID_puestoControl', function (Blueprint $table) {
            $table->id();
            $table->string('puesto');
            $table->enum('estatus', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
        });

        // Insertar datos en RID_puestoControl
        DB::table('RID_puestoControl')->insert([
            ['puesto' => 'Director de Migración'],
            ['puesto' => 'Subdirector de Migración'],
            ['puesto' => 'Jefe de Oficina'],
            ['puesto' => 'Analista de Procesos'],
            ['puesto' => 'Coordinador de Operaciones'],
            ['puesto' => 'Asistente Administrativo'],
            ['puesto' => 'Oficial de Registro'],
            ['puesto' => 'Agente de Atención al Cliente'],
            ['puesto' => 'Supervisor de Campo'],
            ['puesto' => 'Técnico en TI'],
            // Agrega el resto de los puestos aquí
        ]);

        // Crear tabla RID_afinidad
        Schema::create('RID_afinidad', function (Blueprint $table) {
            $table->id();
            $table->string('afinidad');
            $table->enum('estatus', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
        });

        // Insertar datos en RID_afinidad
        DB::table('RID_afinidad')->insert([
            ['afinidad' => 'Familiar'],
            ['afinidad' => 'Amigo'],
            ['afinidad' => 'Compañero de Trabajo'],
            ['afinidad' => 'Conocido'],
            ['afinidad' => 'Otro'],
        ]);

        // Crear tabla RID_migrante
        Schema::create('RID_migrante', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('numero_documento')->unique();
            $table->integer('pais_id');
            $table->integer('puesto_id');
            $table->integer('afinidad_id');
            $table->enum('estatus', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
            $table->foreign('pais_id')->references('id')->on('RID_paises');
            $table->foreign('puesto_id')->references('id')->on('RID_puestoControl');
            $table->foreign('afinidad_id')->references('id')->on('RID_afinidad');
        });

        // Crear tabla RID_ProcessLogs
        Schema::create('RID_ProcessLogs', function (Blueprint $table) {
            $table->id();
            $table->integer('migrante_id');
            $table->string('proceso');
            $table->text('detalle');
            $table->enum('estatus', ['Exitoso', 'Fallido'])->default('Exitoso');
            $table->timestamps();
            $table->foreign('migrante_id')->references('id')->on('RID_migrante');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('RID_ProcessLogs');
        Schema::dropIfExists('RID_migrante');
        Schema::dropIfExists('RID_afinidad');
        Schema::dropIfExists('RID_puestoControl');
        Schema::dropIfExists('RID_paises');
        Schema::dropIfExists('RID_regiones');
    }
}
