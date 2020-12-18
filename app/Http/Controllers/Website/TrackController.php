<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Request;
use Carbon\Carbon;

use App\User;
use App\Models\Track;
use App\Models\TrackCategory;
use App\Models\Session;
use App\Models\Page;
use App\Models\Event;
use App\Models\Domain;
use App\Models\SessionUpload;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TrackController extends Controller
{
    public function trackset()
    {
        try {
            $category = strtolower(Request::get('category'));
        
            $isExist = TrackCategory::where('name', $category)->count();
            if ($isExist) {
                $categoryData = TrackCategory::where('name', $category)->first();
            } else {
                $categoryData = TrackCategory::create([
                    'name'  =>  $category
                ]);
            }

            $id = Request::get('id');
            $siteId = Request::get('siteId');
            $title = Request::get('title');
            $color = Request::get('color');
            $description = Request::get('description');
            $hashtag = Request::get('hashtag');

            if ($id == 0) {
                Track::create([
                    'domain_id'     =>  $siteId,
                    'category_id'   =>  $categoryData->id,
                    'title'         =>  $title,
                    'color'         =>  $color,
                    'description'   =>  $description,
                    'hashtag'       =>  $hashtag
                ]);
            } else {
                $data = Track::find($id);
                $data->category_id = $categoryData->id;
                $data->title = $title;
                $data->color = $color;
                $data->description = $description;
                $data->hashtag = $hashtag;
                $data->save();
            }

            return response()->json(Track::where('domain_id', $siteId)->with('category')->get());
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function trackget()
    {
        try {
            $id = Request::get('id');

            $data['data'] = [];

            $data['items'] = Track::where('domain_id', $id)->with('category')->get();
            $data['count'] = Session::where('domain_id', $id)->count() / Track::where('domain_id', $id)->count();
            $data['data']['items'] = Track::where('domain_id', $id)->with('category')->get();
            $data['data']['count'] = Session::where('domain_id', $id)->count() / Track::where('domain_id', $id)->count();

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function trackdel()
    {
        try {
            Track::find(Request::get('id'))->delete();
            return response()->json(Track::where('domain_id', Request::get('siteId'))->with('category')->get());
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function get()
    {
        try {
            $id = Request::get('id');

            $data['categoryData'] = TrackCategory::get();
            $data['trackData'] = Track::where('domain_id', $id)->get();
            $data['eventData'] = $events = Event::where('domain_id', $id)->get();
            $data['pageData'] = Page::where('domain_id', $id)->get();
            $data['domain'] = Domain::where('id', $id)->with('user')->with('admin')->first();
            $sessions = [];
            foreach($events as $event) {
              $sessionData = $event->sessions();
              foreach($sessionData as $session) {
                $sessions[] = $session;
              }
              $event->sessions = $sessionData;
            }
            $data['items'] = $sessions;
            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function set()
    {
        try {
            $item = json_decode(Request::get('item'));
            $id = $item->id;
            $siteId=$item->siteId;

            $title = $item->title;
            $category_id = $item->category_id;
            $track_id = $item->track_id;
            $event_id = $item->event_id;
            $button = $item->button;
            $pageType = $item->pageType;
            $description = $item->description;
            $hashtag = $item->hashtag;
            $presenter = json_encode($item->presenter);
            $url = $item->url;
            $vimeo_video_id = $item->vimeo_video_id;
            
            $user = Auth::user();

            $event = Event::find($event_id);

            $eventDate = \DateTime::createFromFormat('Y-m-d', $event->date, $user->userTimezone());

            $strStart = explode(':', $item->start);

            $startdate = clone $eventDate;
            $startdate->setTime((($strStart[2] == "PM") ? intval($strStart[0]) + 12 : $strStart[0]), $strStart[1]);

            $strEnd = explode(':', $item->end);

            $enddate = clone $eventDate;
            $enddate->setTime((($strEnd[2] == "PM") ? intval($strEnd[0]) + 12 : $strEnd[0]), $strEnd[1]);
            
            $startdate = $user->userTimeToDB($startdate);
            $enddate = $user->userTimeToDB($enddate);
            
            $start = $startdate->format('H:i');
            $end = $enddate->format('H:i');
            
            if ($item->option == 1) {
                $file = $item->link;
            } else {
                $image = Request::file('file');
                if ($image) {
                    $new_name = rand() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('data/'), $new_name);
                    $file = $new_name;
                }
            }

            if ($id == 0) {
                $data = Session::create([
                    'domain_id'     =>  $siteId,
                    'category_id'   =>  $category_id,
                    'track_id'      =>  $track_id,
                    'event_id'      =>  $event_id,
                    'title'         =>  $title,
                    'button'        =>  $button,
                    'pageType'      =>  $pageType,
                    'description'   =>  $description,
                    'hashtag'       =>  $hashtag,
                    'presenter'     =>  $presenter,
                    'url'           =>  $url,
                    'start'         =>  $start,
                    'end'           =>  $end,
                    'upload_type'   =>  $item->option,
                    'asset'         =>  $file
                ]);
            } else {
                $data = Session::find($id);
                $data->category_id = $category_id;
                $data->track_id = $track_id;
                $data->event_id = $event_id;
                $data->title = $title;
                $data->button = $button;
                $data->pageType = $pageType;
                $data->description = $description;
                $data->hashtag = $hashtag;
                $data->presenter = $presenter;
                $data->url = $url;
                $data->start = $start;
                $data->end = $end;
                $data->vimeo_video_id = $vimeo_video_id;
                $data->upload_type = $item->option;
                $data->asset = $file;
                $data->save();
            }

            $sessionData = Event::where('domain_id', $siteId)->orderBy('date', 'asc')->with('session.track')->with('session.event')->get();
            foreach($sessionData as $event) {
                foreach($event->session as $session) {
                    $sessions[] = $session;
                }
            }
            $data = $this->paginate($sessions);

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function clone()
    {
        try {
            $data = Session::find(Request::get('id'));
            $data->replicate()->save();
            
            $sessionData = Event::where('domain_id', Request::get('siteId'))->orderBy('date', 'asc')->with('session.track')->with('session.event')->get();
            foreach($sessionData as $event) {
                foreach($event->session as $session) {
                    $sessions[] = $session;
                }
            }
            $data = $this->paginate($sessions);

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }


    public function active()
    {
        try {
            $data = Session::find(Request::get('id'));
            $data->stream_active = true;
            $data->save();
            return response()->json(
            );
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function del()
    {
        try {
            Session::find(Request::get('id'))->delete();
                        
            $sessionData = Event::where('domain_id', Request::get('siteId'))->orderBy('date', 'asc')->with('session.track')->with('session.event')->get();
            foreach($sessionData as $event) {
                foreach($event->session as $session) {
                    $sessions[] = $session;
                }
            }
            $data = $this->paginate($sessions);

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function getSessions() {
        try {
            $sessionData = Event::where('domain_id', Request::get('id'))->orderBy('date', 'asc')->with('session.track')->with('session.event')->get();
            foreach($sessionData as $event) {
                foreach($event->session as $session) {
                    $sessions[] = $session;
                }
            }
            $data = $this->paginate($sessions);

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function managerset()
    {
        try {
            $id = Request::get('id');
            $siteId=Request::get('siteId');

            $date = date('Y/m/d', Request::get('date'));

            $startStr = explode(':', Request::get('start'));
            $start = $startStr[0].':'.$startStr[1].' '.$startStr[2];
            $start = date('H:i', strtotime($start));

            $endStr = explode(':', Request::get('end'));
            $end = $endStr[0].':'.$endStr[1].' '.$endStr[2];
            $end = date('H:i', strtotime($end));

            if ($id == 0) {
                Event::create([
                    'domain_id' =>  $siteId,
                    'date'      =>  $date,
                    'start'     =>  $start,
                    'end'       =>  $end,
                    'timezone'  =>  1
                ]);
            } else {
                $data = Event::find($id);
                $data->domain_id = $siteId;
                $data->date = $date;
                $data->start = $start;
                $data->end = $end;
                $data->timezone = 1;
                $data->save();
            }

            return response()->json(Event::where('domain_id', $siteId)->get());
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function managerget()
    {
        try {
            $id = Request::get('id');
            $domain = Domain::find($id);
            $data['items'] = Event::where('domain_id', $id)->get();
            $data['categoryData'] = Track::where('domain_id', $id)->get();

            $start = Carbon::parse($domain->start);
            $end = Carbon::parse($domain->end);
            $now = Carbon::now();
            $countStart = $start->diffInDays($now);
            $countEnd = $end->diffInDays($now);
            if ($start->gt($now)) {
                $data['countStart'] = $countStart;
                $data['countEnd'] = $countEnd;
            } else if ($now->gt($end)) {
                $data['countStart'] = 0;
                $data['countEnd'] = 0;
            } else {
                $data['countStart'] = 0;
                $data['countEnd'] = $countEnd;
            }

            return response()->json($data);
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function managerdel()
    {
        try {
            Event::find(Request::get('id'))->delete();
            return response()->json(Event::where('domain_id', Request::get('siteId'))->get());
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function paginate($items, $perPage = 8, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function getUpload()
    {
        try {
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function delUpload()
    {
        try {
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

    public function upload()
    {
        try {
            $option = Request::get('option');
            $id = Request::get('id');
            
            if ($option == 1) {

            } else {

            }
        } catch(\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
}
