<?php
namespace App\Contracts\Employee;

interface EmployeeServiceInterface{


//get details. update, remove
    
public function getAllEmployeeDetails();
public function pendingEmployeeDetails();
public function update($id, $data);
public function deleteEmloyeeDecision($id);

}