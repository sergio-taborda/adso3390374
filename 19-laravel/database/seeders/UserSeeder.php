
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class UserSeeder extends Seeder
{
    /**
     * Generate 50 users from Random User and download their profile photos
     * into the project's public/images directory.
     */
    public function run(): void
    {
        $response = Http::timeout(60)
            ->retry(3, 1000)
            ->get('https://randomuser.me/api/', [
                'results' => 50,
                'nat' => 'es',
                'inc' => 'gender,name,email,dob,phone,picture,login',
            ]);

        if (!$response->successful()) {
            throw new RuntimeException(
                'No fue posible obtener los perfiles desde Random User.'
            );
        }

        $people = $response->json('results');

        if (!is_array($people) || count($people) !== 50) {
            throw new RuntimeException(
                'Random User no devolvió exactamente 50 perfiles.'
            );
        }

        $imagesDirectory = public_path('images');

        if (!is_dir($imagesDirectory) && !mkdir($imagesDirectory, 0755, true) && !is_dir($imagesDirectory)) {
            throw new RuntimeException(
                'No se pudo crear la carpeta public/images.'
            );
        }

        foreach ($people as $index => $person) {
            $gender = $person['gender'] ?? null;

            if (!in_array($gender, ['male', 'female'], true)) {
                throw new RuntimeException(
                    'Random User devolvió un género no válido.'
                );
            }

            $firstName = $person['name']['first'] ?? '';
            $lastName = $person['name']['last'] ?? '';
            $fullname = trim($firstName . ' ' . $lastName);
            $email = $person['email'] ?? null;
            $imageUrl = $person['picture']['large'] ?? null;
            $uuid = $person['login']['uuid'] ?? null;

            $birthdate = isset($person['dob']['date'])
                ? date('Y-m-d', strtotime($person['dob']['date']))
                : null;

            $phone = $person['phone'] ?? '';

            if (!$fullname || !$email || !$imageUrl || !$uuid || !$birthdate) {
                throw new RuntimeException(
                    'Random User devolvió un perfil incompleto.'
                );
            }

            // Descargar la fotografía del mismo perfil y género.
            $imageResponse = Http::timeout(30)
                ->retry(3, 1000)
                ->get($imageUrl);

            if (!$imageResponse->successful() || !$imageResponse->body()) {
                throw new RuntimeException(
                    "No se pudo descargar la fotografía de {$fullname}."
                );
            }

            $imageName = $uuid . '.jpg';

            $imageFullPath = $imagesDirectory
                . DIRECTORY_SEPARATOR
                . $imageName;

            if (file_put_contents(
                $imageFullPath,
                $imageResponse->body()
            ) === false) {
                throw new RuntimeException(
                    "No se pudo guardar la fotografía de {$fullname}."
                );
            }

            // La columna photo guarda la ruta relativa dentro de public.
            $user = new User();

            $user->document = $this->generateUniqueDocument();
            $user->fullname = $fullname;
            $user->gender = $gender;
            $user->birthdate = $birthdate;
            $user->photo = 'images/' . $imageName;
            $user->phone = $phone;
            $user->email = $email;
            $user->password = '12345';
            $user->email_verified_at = now();

            // Se conserva una cuenta administradora como en el seeder original.
            $user->role = $index === 0 ? 'Administrator' : 'customer';

            $user->save();
        }

        $this->command?->info(
            'Se crearon 50 usuarios y sus fotografías quedaron guardadas en public/images.'
        );
    }

    /**
     * Generate a unique 9-digit document number.
     */
    private function generateUniqueDocument(): int
    {
        do {
            $document = random_int(100000000, 999999999);
        } while (User::where('document', $document)->exists());

        return $document;
    }
}
