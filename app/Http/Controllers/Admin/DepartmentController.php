<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Repositories\DepartmentRepository;

class DepartmentController extends Controller
{
    protected $departmentRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\DepartmentRepository $departmentRepository
     * @return void
     */
    public function __construct(
        DepartmentRepository $departmentRepository
    ) {
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * Show department by id.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        $department = $this->departmentRepository->get($id);

        return view('admin.department.detail')->with([
            'department' => $department,
        ]);
    }

     /**
     * Show department list.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $departments = $this->departmentRepository->getList();

        return view('admin.department.list')->with([
            'departments' => $departments,
        ]);
    }

    /**
     * Edit department page.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $department = $this->departmentRepository->get($id);

        return view('admin.department.edit')->with([
            'department'  => $department,
        ]);
    }

    /**
     * Update department by id.
     *
     * @param  \App\Http\Requests\DepartmentRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(DepartmentRequest $request, int $id)
    {
        $validated = $request->validated();
        $this->departmentRepository->update($id, $validated);

        return redirect("/admin/departments/{$id}");
    }

    /**
     * Create department page.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.department.create');
    }

    /**
     * Store department.
     *
     * @param  \App\Http\Requests\DepartmentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(DepartmentRequest $request)
    {
        $validated = $request->validated();

        if (empty($validated['manager_id'])) {
            $validated['manager_id'] = 0;
        }

        $department = $this->departmentRepository->create($validated);

        return redirect("admin/departments/{$department->id}");
    }
}