<?php

namespace App\Http\Controllers\Task;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TaskProject;
use App\Models\Task;
use Mail;

class TaskController extends Controller
{
    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        return $data;
    }
    public function addProject()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $project_managers = DB::select("SELECT * FROM user WHERE role <> 'vendor' AND role <> 'user'");
            $data['project_managers'] = json_decode(json_encode($project_managers),true);

            $team_members = DB::select("SELECT * FROM user WHERE role <> 'vendor'");
            $data['team_members'] = json_decode(json_encode($team_members),true);
            return view('Task/add_project_task',$data);
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            //print_r($_POST);
            $taskProject = new TaskProject();
            $project_team_members = json_encode($_POST['project_team_members']);
            $insertTask = 
            [
                'project_name' => $_POST['project_name'],
                'project_status' => $_POST['project_status'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'project_manager' => $_POST['project_manager'],
                'project_team_members' => $project_team_members,
                'description' => $_POST['description'],
                'created_by' => session('user_id'),
            ];
            $tasks = $taskProject->insert($insertTask);
            return redirect('add_project_task')->with('message', 'Project Added Successfully');
        }
    }
    public function editProject($project_id)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $project_managers = DB::select("SELECT * FROM user WHERE role <> 'vendor' AND role <> 'user'");
            $data['project_managers'] = json_decode(json_encode($project_managers),true);

            $team_members = DB::select("SELECT * FROM user WHERE role <> 'vendor'");
            $data['team_members'] = json_decode(json_encode($team_members),true);

            $project_details = DB::select("SELECT * FROM tk_projects WHERE id = $project_id");
            $data['project_details'] = json_decode(json_encode($project_details),true);
            return view('Task/edit_project_task',$data);
        }
    }
    public function updateProject()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            //print_r($_POST);
            $taskProject = new TaskProject();
            $project_team_members = json_encode($_POST['project_team_members']);
            $updateTask = 
            [
                'project_name' => $_POST['project_name'],
                'project_status' => $_POST['project_status'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'project_manager' => $_POST['project_manager'],
                'project_team_members' => $project_team_members,
                'description' => $_POST['description'],
                'updated_by' => session('user_id'),
            ];
            $tasks = $taskProject->where('id', $_POST['project_id'])->update($updateTask);
            return redirect('viewAllProjects')->with('message', 'Project Updated Successfully');
        }
    }
    public function deleteProject($project_id)
    {
        $taskProject = new TaskProject();
        $tasks = $taskProject->where('id',$project_id)->delete();
        return redirect('viewAllProjects')->with('message', 'Task Deleted Successfully');
    }
    public function viewProjects()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        $project_details = DB::select("SELECT * FROM tk_projects");
        $data['project_details'] = json_decode(json_encode($project_details),true);
        foreach($data['project_details'] as $project_details)
        {
            
        }
        return view('Task/view_all_projects',$data);
    }
    public function addTask()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $project_details = DB::select("SELECT * FROM tk_projects");
            $data['project_details'] = json_decode(json_encode($project_details),true);
            return view('Task/add_new_task',$data);
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            //print_r($_POST);
            $taskProject = new Task();
            $insertTask = 
            [
                'task_name' => $_POST['task_name'],
                'status' => $_POST['task_status'],
                'description' => $_POST['description'],
                'project_id' => $_POST['project_id'],
                'created_by' => session('user_id'),
            ];
            $tasks = $taskProject->insert($insertTask);
            return redirect('add_new_task')->with('message', 'Task Added Successfully');
        }
    }
    public function editTask($task_id)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $task_detail = DB::select("SELECT * FROM tasks WHERE task_id = $task_id");
            $data['task_detail'] = json_decode(json_encode($task_detail),true);

            $project_details = DB::select("SELECT * FROM tk_projects");
            $data['project_details'] = json_decode(json_encode($project_details),true);
            return view('Task/edit_new_task',$data);
        }
    }
    public function updateTask()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            //print_r($_POST);
            $taskProject = new Task();
            $updateTask = 
            [
                'task_name' => $_POST['task_name'],
                'status' => $_POST['task_status'],
                'description' => $_POST['description'],
                'project_id' => $_POST['project_id'],
                'updated_by' => session('user_id'),
            ];
            $tasks = $taskProject->where('task_id', $_POST['task_id'])->update($updateTask);
            return redirect('my_task')->with('message', 'Task Updated Successfully');
        }
    }
    public function deleteTask($task_id)
    {
        $taskProject = new Task();
        $tasks = $taskProject->where('task_id',$task_id)->delete();
        return redirect('my_task')->with('message', 'Task Deleted Successfully');
    }
    public function my_task()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        $project_details = DB::select("SELECT * FROM tasks,tk_projects WHERE tasks.project_id = tk_projects.id");
        $data['project_details'] = json_decode(json_encode($project_details),true);
        return view('Task/view_all_tasks',$data);
    }
}
?>