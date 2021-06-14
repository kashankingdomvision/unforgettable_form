<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Template;
use App\TemplateDetail;
use App\Category;
use App\User;
use App\Supplier;
use App\BookingMethod;
use App\Currency;
use App\Season;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
class TemplateController extends Controller
{
    public $pagination = 10;
    public $data = [];
    public function __Construct()
    {       
      $this->pagination = 10;
      
      $data['categories']       = Category::all()->sortBy('name');
      $data['supervisors']      = User::where('role_id', 5)->get()->sortBy('name');
      $data['suppliers']        = Supplier::all()->sortBy('name');
      $data['booking_methods']  = BookingMethod::all()->sortBy('id');
      $data['currencies']       = Currency::all()->sortBy('id');
      $data['users']            = User::all()->sortBy('name');
      $data['seasons']          = Season::all();
      $this->data = $data;
    }
    
    public function getArrayTemplateDetails($quote)
    {
      return [
        'date_of_service'   => (isset($quote['date_of_service']))?Carbon::parse(str_replace('/', '-', $quote['date_of_service']))->format('Y-m-d') : null,   
        'service_details'   => $quote['service_details'],    
        'category_id'       => $quote['category_id'],          
        'supplier'          => $quote['supplier_id'],          
        'booking_date'      => (isset($quote['booking_date']))?Carbon::parse(str_replace('/', '-', $quote['booking_date']))->format('Y-m-d') : null,      
        'booking_due_date'  => (isset($quote['booking_due_date']))?Carbon::parse(str_replace('/', '-', $quote['booking_due_date']))->format('Y-m-d') : null,  
        'booking_method'    => $quote['booking_method_id'], 
        'booked_by'         => $quote['booked_by_id'],
        'booking_refrence'  => $quote['booking_reference'],
        'booking_type'      => $quote['booking_type'],
        'comments'          => $quote['comments'],
        'supplier_currency' => $quote['currency_id'],
        'cost'              => $quote['cost'],
        'supervisor_id'     => $quote['supervisor_id'],
        'added_in_sage'     => (isset($quote['add_in_sag']))? (($quote['add_in_sag'] == true)? 1 : 0): 0,   
      ];     
    }
    
    
    public function index()
    {
      $data['templates'] = Template::paginate($this->pagination);
      return view('template.listing', $data);
    }
    
    public function create()
    {
      return view('template.create', $this->data);
    }
    
    public function store(Request $request)
    {
      $template = Template::create([
        'user_id' => Auth::id(),
        'title' => $request->template_name,
        'season_id' => $request->season_id
      ]);
      
      foreach ($request->quote as $quote) {
        $data = $this->getArrayTemplateDetails($quote);
        $data['template_id']  = $template->id;
        TemplateDetail::create($data);
      }

      return redirect()->route('template.index')->with('success_message', 'template created successfully');
    }
    
    public function detail($id)
    {
        $template = Template::findOrFail(decrypt($id));
        $data['template'] = $template;
        return view('template.details',$data);
    }
    
    public function destroy($id)
    {
      $template = Template::findOrFail(decrypt($id));
      $template->delete();
      return redirect()->back()->with('success_message', 'template deleted successfully');
    }
    
    public function edit($id)
    {
       $template = Template::findOrFail(decrypt($id));
       $data = $this->data;
       $data['template'] = $template;

       return view('template.edit', $data);
    }
    
    public function update(Request $request, $id)
    {
        $template = Template::findOrFail(decrypt($id));
        $template->update([
          'title'     => $request->template_name,
          'season_id' => $request->season_id,
        ]);
        
        foreach ($request->quote as $quote) {
            $data = $this->getArrayTemplateDetails($quote);
            $data['template_id'] = $template->id;
            $key = (isset($quote['key']))? decrypt($quote['key']) : 0;
            $template->getTemplateDetails()->updateOrCreate(['id' => $key],$data);
        }
        
      return redirect()->route('template.index')->with('success_message', 'template deleted successfully');
        
    }
    
    public function call_template($id)
    {
        $template = Template::findOrFail($id);
        $data = $this->data;
        $data['template'] = $template;
        $return['template'] = $template;
        $return['template_view'] = View::make('template.quote_partial_view', $data)->render();
        return response()->json($return);
    }
    
}
