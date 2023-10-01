<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Repositories\AdminRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    protected $adminRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\AdminRepository $adminRepository
     * @return void
     */
    public function __construct(
        AdminRepository $adminRepository
    ) {
        $this->adminRepository = $adminRepository;
    }

    /**
     * Get admin by id.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        $user = $this->adminRepository->get($id);

        return view('admin.admin-user.detail')->with([
            'user' => $user,
        ]);
    }

    /**
     * Get admin list.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $users = $this->adminRepository->getList(
            $this->getPaginate($request)
        );

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('admin.admin-user.list')->with([
            'users' => $users,
        ]);
    }

    /**
     * Edit admin page.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $user = $this->adminRepository->get($id);

        return view('admin.admin-user.edit')->with([
            'user'  => $user,
        ]);
    }

    /**
     * Update admin by id.
     *
     * @param  \App\Http\Requests\AdminRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AdminRequest $request, int $id)
    {
        $validated = $request->validated();

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password'], ['cost' => 4]);

        }

        $this->adminRepository->update($id, $validated);

        return redirect("admin/admin-users/{$id}");
    }

    /**
     * Create admin page.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.admin-user.create');
    }

    /**
     * Create admin.
     *
     * @param  \App\Http\Requests\AdminRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AdminRequest $request)
    {
        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password'], ['cost' => 4]);

        $user = $this->adminRepository->create($validated);

        return redirect("admin/admin-users/{$user->id}");
    }
}