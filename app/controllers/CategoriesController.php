<?php
class CategoriesController extends Controller
{
    public function add()
    {
        // You can load a view or handle form submission here
        $this->view('categories/add');
    }
    // Add more methods as needed (index, edit, delete, etc.)
}
