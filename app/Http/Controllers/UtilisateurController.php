<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cours;
use App\Models\session;
use App\Models\Formation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class UtilisateurController extends Controller
{
    public function index()
    {
        return view('Formateur.homeformateur');
    }
    // public function __construct()

    // {
    //     $this->middleware('auth');
    //     // $this->middleware('role:Etudiant');
    // }
    protected function createf(Request $rqt)
    {

        $user = new User();
        $user->name = $rqt->name;
        $user->email = $rqt->email;
        $user->profil = $rqt->profil;
        $user->password = Hash::make($rqt->password);

        $user->save();
        $user->roles()->attach(3);
        return Redirect::route('listeformateur');
    }

    public function ajouterC()
    {
        $form = Utilisateur::where('email', Auth::user()->email)->first();
        $id_formateur = $form->id;
        $formateur = Utilisateur::where('id', $id_formateur)->get();
        $formations = Session::where('id_formateur', $id_formateur)->get();
        $data = Cours::where('id_formateur', $id_formateur)->get();

        return view('Formateur.ajouterCours', ['formateurs' => $formateur, 'formations' => $formations, 'data' => $data]);
    }


    public function storeC(Request $request)
    {

        $formation = Formation::where('id', $request->id_formation)->latest()->first();

        $data = new Cours();
        $data->titre = $request->titre;
        $data->description = $request->description;
        $file = $request->file;
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $data->id_formation = $formation->id;
        $formateur = Utilisateur::where('email', Auth::user()->email)->first();

        $data->id_formateur = $formateur->id;
        $request->file->move('assets', $filename);
        $data->file = $filename;
        $data->save();

        return redirect()->back();
    }


    public function telechargerC($file)
    {

        return response()->download(public_path('assets/' . $file));
    }

    public function getEtudiant()
    {
        $form = Utilisateur::where('email', Auth::user()->email)->first();
        $id_formateur = $form->id;

        $listFormationEtudiants = array();

        $formationFormateur = session::where('id_formateur', $id_formateur)->get();

        foreach ($formationFormateur as $formationFormateur) {

            $etudiants = Utilisateur::where('formation', $formationFormateur->formation)->get();
            array_push($listFormationEtudiants, (object)["etudiants" => $etudiants, "formation" => $formationFormateur->formation]);
        }

        return view('Formateur.etudiants', compact('listFormationEtudiants'));
    }

    public function getAllFormations()
    {

        $formations = Formation::all();

        return view('Etudiant.formations', ['formations' => $formations]);
    }

    public function getFormation(Formation $formation)
    {

        // $id = $rqt->id_formation;
        // $formation = session::find($id);
        // return view('Etudiant.formation',compact('formation'));
        // $form = Utilisateur::where('email',Auth::user()->email)->first();
        // $id_formateur = $form->id;
        $id_formation = $formation->id;

        $formation = Formation::where('id', $id_formation)->get();

        $sessionFormation = session::where('id_formation', $id_formation)->get();
        // $nom = $sessionFormation->where('id_formateur',10)->get();
        // echo $nom;
        $Sessions = array();
        foreach ($sessionFormation as $forma) {
            $formateurs = Utilisateur::where('id', $forma->id_formateur)->get();

            array_push($Sessions, (object)["formateurs" => $formateurs, "formation" => $formation]);
            // $id_formateurs = array($sessionFormation->id_formateur);

            //dd($sessionFormation->id_formateur);

            // $Sessions = array();

            // //$formationFormateur = session::where('id_formateur',$id_formateur)->get();

            // foreach($sessionFormation as $formation){

            //     //foreach($id_formateurs as $id_formateur){

            //     $formateur = Utilisateur::where('id',$id_formateur)->get();

            // array_push($Sessions,(object)["formateur" => $formateur,"formation" =>$formation]);
            //     }      
            // }
            //return view('welcome'); 
            // return Redirect::route("user_formation");

        }
        //return view('welcome');  

        return view('Etudiant.formation', compact('Sessions'));
    }

    public function inscriptionEtudiant(Formation $formation)
    {
        $id_formation = $formation->id;
        $formation = Formation::where('id', $id_formation)->first();

        $nomFormation = $formation->nom_formation;
        return view('Etudiant.inscription', ['formation' => $formation]);
    }

    public function addEtudiant(Request $request)
    {

        //$etudiant = Utilisateur::where('profil',Auth::user()->profil =="etudiant")->first();
        $nom = $request->nom;
        $prenom = $request->prenom;
        $email = $request->email;
        $age = $request->age;
        $cin = $request->cin;
        $formation = $request->formation;
        $tel = $request->tel;
        $niveau = $request->niveau;


        $etudiant = new Utilisateur();
        $etudiant->nom = $nom;
        $etudiant->prenom = $prenom;
        $etudiant->email = $email;
        $etudiant->age = $age;
        $etudiant->cin = $cin;
        $etudiant->formation = $formation;
        $etudiant->tel = $tel;
        $etudiant->niveau = $niveau;
        $etudiant->profil = "etudiant";

        $etudiant->save();

        return Redirect::route('user_listeformations');
    }

    public function listeFormation()
    {
        $etudiants = Utilisateur::where('email', Auth::user()->email)->get();



        // if ($etudiant->is_payer == 'false' || $etudiant->is_payer == "NULL") {

        //     return view('Etudiant.coursNonPaye', ['etudiant' => $etudiant]);
        // } else {
        //     $nomFormation = $etudiant->formation;
        //     $fs = Formation::where('nom_formation', $nomFormation)->get();

        //     foreach ($fs as $f) {
        //         $id_formation = $f->id;
        //         $data = Cours::where('id_formation', $id_formation)->get();

        //         //$file = $data->file;

        return view('Etudiant.mesCours', ['formationInscrit' => $etudiants]); //mes cours c-a-d mes formations
    }

    public function CoursDeFormation($formation)
    {
        $etudiant = Utilisateur::where('email', Auth::user()->email)->where('formation', $formation)->latest()->first();
        if ($etudiant->is_payer == 'false' || $etudiant->is_payer == null) {

            return view('Etudiant.coursNonPaye', ['etudiant' => $etudiant]);
        } else {
            $fs = Formation::where('nom_formation', $formation)->latest()->first();
            $data = Cours::where('id_formation', $fs->id)->get();
            return view('Etudiant.mesCours2', ['Cours' => $data, 'formation' => $formation]); //mes vraies cours 
            //$file = $data->file;
        }
    }
}
