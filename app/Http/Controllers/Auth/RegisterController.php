<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    // ── Static option lists (match signup.php exactly) ────────────────
    private array $collegePrograms = ['ACT', 'BSA', 'BSBA', 'BSCS', 'BSED', 'BEED', 'BSHM', 'BSTM'];
    private array $shsPrograms     = ['ABM', 'HUMSS', 'ICT', 'STEM'];
    private array $jhsPrograms     = ['Junior High School'];
    private array $elemPrograms    = ['Elementary'];

    private array $religions        = ['Catholic', 'Protestant', 'Iglesia ni Cristo', 'Islam', 'Seventh Day Adventist', "Jehovah's Witness", 'Other'];
    private array $civilStatuses    = ['Single', 'Married', 'Widowed', 'Separated', 'Other'];
    private array $preferredContacts= ['Email', 'Phone', 'FB/Messenger', 'Other'];
    private array $highestDegrees   = ['Elementary Graduate', 'Junior High School Graduate', 'Senior High School Graduate', 'Associate Degree', "Bachelor's Degree", "Master's Degree", 'Doctorate', 'Other'];
    private array $academicPerfs    = ['Excellent', 'Very Good', 'Good', 'Other'];
    private array $employmentStatuses = ['Employed', 'Unemployed', 'Other'];
    private array $employmentTypes  = ['Locally', 'Abroad', 'Self-employed', 'Other'];
    private array $jobRelatedOptions = ['Yes', 'No', 'Other'];
    private array $changesNeeded    = [
        'Update curriculum', 'Upgrade facilities', 'Provide job placement services',
        'Enhance industry partnerships', 'Offer career counseling',
        'Improve soft skills training', 'Other',
    ];

    /** GET /register */
    public function showForm()
    {
        $batangasTowns = $this->batangasTowns();

        return view('auth.register', [
            'collegePrograms'   => $this->collegePrograms,
            'shsPrograms'       => $this->shsPrograms,
            'jhsPrograms'       => $this->jhsPrograms,
            'elemPrograms'      => $this->elemPrograms,
            'religions'         => $this->religions,
            'civilStatuses'     => $this->civilStatuses,
            'preferredContacts' => $this->preferredContacts,
            'highestDegrees'    => $this->highestDegrees,
            'academicPerfs'     => $this->academicPerfs,
            'employmentStatuses'=> $this->employmentStatuses,
            'employmentTypes'   => $this->employmentTypes,
            'jobRelatedOptions' => $this->jobRelatedOptions,
            'changesNeeded'     => $this->changesNeeded,
            'batangasTowns'     => $batangasTowns,
            'graduationYears'   => range(date('Y'), 1941),
            'ages'              => range(5, 120),
        ]);
    }

    /** POST /register */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        // Flatten changes_needed array → comma-separated string
        if (isset($data['changes_needed']) && is_array($data['changes_needed'])) {
            $data['changes_needed'] = implode(',', $data['changes_needed']);
        }

        // Map form 'sex' field to model 'gender' column
        $data['gender'] = $data['sex'] ?? null;
        unset($data['sex']);

        // Set account defaults
        $data['status'] = 'pending';
        $data['role']   = 'user';

        // Remove confirm field (not a DB column)
        unset($data['password_confirmation']);

        $user = User::create($data);

        // Show "Account Under Review" modal (same as legacy signup.php)
        return view('auth.register-pending', ['email' => $user->email]);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function batangasTowns(): array
    {
        $towns = [
            'Agoncillo', 'Alitagtag', 'Balayan', 'Balete', 'Batangas City', 'Bauan',
            'Calaca', 'Calatagan', 'Cuenca', 'Ibaan', 'Laurel', 'Lemery', 'Lian',
            'Lipa City', 'Lobo', 'Mabini', 'Malvar', 'Mataasnakahoy', 'Nasugbu',
            'Padre Garcia', 'Rosario', 'San Jose', 'San Juan', 'San Luis', 'San Nicolas',
            'San Pascual', 'Santa Teresita', 'Santo Tomas City', 'Taal', 'Talisay',
            'Tanauan City', 'Taysan', 'Tingloy', 'Tuy',
        ];
        sort($towns);
        return $towns;
    }
}
