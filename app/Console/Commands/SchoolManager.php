<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SchoolManager extends Command
{
    protected $signature = 'school:manage';
    protected $description = 'Manage school users and tasks';

    public function handle()
    {
        $this->info('Welcome to the School Management System');
        
        // Authentication
        $email = $this->ask('Enter your email');
        $password = $this->secret('Enter your password');

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $this->info('Authentication successful');
            $user = Auth::user();

            // Check role
            switch ($user->role) {
                case 'admin':
                    $this->showAdminMenu();
                    break;
                case 'formateur':
                    $this->addNotes();
                    break;
                case 'etudiant':
                    $this->viewNotes($user);
                    break;
                default:
                    $this->error('Invalid user role');
                    break;
            }
        } else {
            $this->error('Invalid credentials');
        }
    }

    private function showAdminMenu()
    {
        $choice = $this->choice(
            'What would you like to do?',
            ['Create Formateur', 'Create Student', 'View Average Notes', 'Exit']
        );

        switch ($choice) {
            case 'Create Formateur':
                $this->createFormateur();
                break;
            case 'Create Student':
                $this->createStudent();
                break;
            case 'View Average Notes':
                $this->viewAverageNotes();
                break;
            case 'Exit':
                $this->info('Goodbye!');
                return;
        }

        $this->showAdminMenu();
    }

    private function createFormateur()
    {
        $data = $this->gatherUserData('formateur');
        User::create($data);
        $this->info('Formateur created successfully');
    }

    private function createStudent()
    {
        $data = $this->gatherUserData('etudiant');
        User::create($data);
        $this->info('Student created successfully');
    }

    private function addNotes()
    {
        $studentId = $this->ask('Enter student ID');
        $student = User::find($studentId);
    
        if (!$student) {
            $this->error('Student not found');
            return;
        }
    
        if ($student->note1 !== null && $student->note2 !== null) {
            $this->error('Maximum notes limit reached for this student');
            return;
        }
    
        $note1 = $this->ask('Enter note 1');
        $note2 = $this->ask('Enter note 2');
    
        $student->update([
            'note1' => $note1,
            'note2' => $note2,
        ]);
    
        $this->info('Notes added successfully');
    }
    
    private function viewNotes($student)
    {
        $notes = $student->notes()->get();

        if ($notes->isEmpty()) {
            $this->info('No notes found');
            return;
        }

        $this->table(['Note 1', 'Note 2'], $notes->toArray());
    }

    private function gatherUserData($role)
    {
        return [
            'nom' => $this->ask('Enter nom'),
            'prenom' => $this->ask('Enter prenom'),
            'CIN' => $this->ask('Enter CIN'),
            'adresse' => $this->ask('Enter adresse'),
            'telephone' => $this->ask('Enter telephone'),
            'date_naissance' => $this->ask('Enter date de naissance'),
            'sexe' => $this->ask('Enter sexe'),
            'email' => $this->ask('Enter email'),
            'password' => Hash::make($this->secret('Enter password')),
            'role' => $role,
        ];
    }

    private function viewAverageNotes()
    {
        $teachers = User::where('role', 'formateur')->with('students')->get();

        $result = $teachers->map(function ($teacher) {
            $students = $teacher->students;
            $avgNote = $students->map(function ($student) {
                return ($student->note1 + $student->note2) / 2;
            })->average();

            return [
                'etudiant' => $students->nom . ' ' . $students->prenom,
                'average_note' => $avgNote,
            ];
        });

        $this->table(['Etudiant', 'Average Note'], $result);
    }
}
