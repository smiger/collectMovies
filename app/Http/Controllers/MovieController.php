<?php
/**
 * Created by PhpStorm.
 * User: user232
 * Date: 2018/4/24
 * Time: 17:30
 */

namespace App\Http\Controllers;


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use App\Tools\CustomPage;

class MovieController extends Controller
{


    public function feedback(Request $request)
    {
        date_default_timezone_set("Asia/Shanghai");
        if($request->has('message')){
            $message=$request->input("message");
            $request->setTrustedProxies(array('61.151.179.84'),Request::HEADER_X_FORWARDED_ALL);
            $res = DB::table('feedback')->insert(
                [
                    'content'=>$message,
                    'createtime'=>date('y-m-d H:i:s',time()),
                    'ip'=>$request->getClientIp()
                ]
            );
            if($res){
                $data=[
                    'status'=>1,
                    'msg'=>'反馈成功'
                ];

            }else{
                $data=[
                    'status'=>0,
                    'msg'=>'反馈失败'
                ];
            }

        }else{
            $data=[
                'status'=>0,
                'msg'=>'反馈失败'
            ];
        }
        return response()->json($data);
    }


    /// /////////////////////采集 begin///////////////////
    public function fenlei(Request $request){
//        http://127.0.0.1/fenlei?ac=list&cjflag=2fc611d3d64b3f597490f24260135e26&cjurl=http%3A%2F%2Fwww.97zyw.com%2Finc%2F97zyck.php&h=&t=&ids=&wd=&type=1&param=
        $param = $request->input();
        $url_param = [];
        $url_param['ac'] = $param['ac'];
        $url_param['t'] = $param['t'];
        $url_param['pg'] = array_key_exists('page', $param) ? $param['page'] : '';
        $url_param['h'] = $param['h'];
        $url_param['ids'] = $param['ids'];
        $url_param['wd'] = $param['wd'];
//        var_dump($url_param['h']);
        if (empty($param['h']) && !empty($param['rday'])) {
            $url_param['h'] = $param['rday'];
        }

        if ($param['ac'] != 'list') {
            $url_param['ac'] = 'videolist';
        }
        $url = $param['cjurl'];
        if (strpos($url, '?') === false) {
            $url .= '?';
        } else {
            $url .= '&';
        }
        $url .= http_build_query($url_param) . base64_decode($param['param']);
        $html = $this->mac_curl_get($url);
        if (empty($html)) {
            return ['code' => 1001, 'msg' => '连接API资源库失败，通常为服务器网络不稳定或禁用了采集'];
        }
        $xml = @simplexml_load_string($html);
        $array_type = [];
        $key=0;
        $res1 = config('vodtype');
        //分类列表
        if($param['ac'] == 'list'){
            foreach($xml->class->ty as $ty){
                $array_type[$key]['type_id'] = (string)$ty->attributes()->id;
                $array_type[$key]['type_name'] = (string)$ty;
                $array_type[$key]['type_result_id'] = array_key_exists(base64_encode($param['cjurl'])."_".$array_type[$key]['type_id'],$res1)?$res1[base64_encode($param['cjurl'])."_".$array_type[$key]['type_id']]:"0";
                $array_type[$key]['type_result_name'] = "请选择分类";
                if ($array_type[$key]['type_result_id'] != "0"){
                    $aa = DB::table("mac_type")->where([
                        ['type_id', '=', $array_type[$key]['type_result_id']]
                    ])->first();
                    if($aa != null) {
                        $aa = $this->objectToArray($aa);
                        $array_type[$key]['type_result_name'] = $aa['type_name'];
                    }
                }

                $key++;
            }
        }
        $array_type = $this->arrayToObject($array_type);
        $mactype = DB::table("mac_type")->get();
        //如果有数据
        $mac_type = [];
        if(!$mactype->isEmpty()) {
            $mactype = $mactype->map(function ($value) {
                return (array)$value;
            })->toArray();
            foreach ($mactype as $key=>$item) {
                $mac_type[$key]['type_id'] = $item['type_id'];
                $mac_type[$key]['type_name'] = $item['type_name'];
            }
        }
        $mac_type = $this->arrayToObject($mac_type);
//        dd($array_type);
        return view("fenlei",['url'=>base64_encode($param['cjurl']), 'array_type'=>$array_type, 'mac_type'=>$mac_type]);
    }
    public function bind(Request $request){
        $res1 = config('vodtype');
        $res2 = $request->post();
        $res = array_merge($res1,$res2);
        ob_start();
        var_export($res);
        $arrStr = ob_get_contents();
        ob_end_clean();
        $config = '<?php' . PHP_EOL
            . 'return ' . $arrStr . ';';
        $path = CONFIG_PATH . 'vodtype.php';
        $res = file_put_contents($path, $config);
        if ($res) {
            echo json_encode(['status' => 200, 'msg' => '修改成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '修改失败']);
        }
    }
    public function collect(Request $request){
        $param = $request->input();
        //var_dump($param);
        if($param['mid']=='' || $param['mid']=='1'){
            $res = $this->vod($param);
            if($res['code']>1){
                echo $res['msg'];
                return;
            }
            $this->vod_data($param,$res);
        }
    }
    public function vod($param)
    {
        if($param['type'] == '1'){
            return $this->vod_xml($param);
        }
    }
    public function vod_xml($param,$html='')
    {
        $url_param = [];
        $url_param['ac'] = $param['ac'];
        $url_param['t'] = $param['t'];
        $url_param['pg'] = array_key_exists('page', $param) ? $param['page'] : '';
        $url_param['h'] = $param['h'];
        $url_param['ids'] = $param['ids'];
        $url_param['wd'] = $param['wd'];
//        var_dump($url_param['h']);
        if (empty($param['h']) && !empty($param['rday'])) {
            $url_param['h'] = $param['rday'];
        }

        if ($param['ac'] != 'list') {
            $url_param['ac'] = 'videolist';
        }
        $url = $param['cjurl'];
        if (strpos($url, '?') === false) {
            $url .= '?';
        } else {
            $url .= '&';
        }
        $url .= http_build_query($url_param) . base64_decode($param['param']);
        $html = $this->mac_curl_get($url);
        if (empty($html)) {
            return ['code' => 1001, 'msg' => '连接API资源库失败，通常为服务器网络不稳定或禁用了采集'];
        }
        $xml = @simplexml_load_string($html);
//        dd($xml);
//        return;
        $vodtype = config('vodtype');

        $array_page = [];
        $array_page['page'] = (string)$xml->list->attributes()->page;
        $array_page['pagecount'] = (string)$xml->list->attributes()->pagecount;
        $array_page['pagesize'] = (string)$xml->list->attributes()->pagesize;
        $array_page['recordcount'] = (string)$xml->list->attributes()->recordcount;
        $array_page['url'] = $url;

        $key = 0;
        $array_data = [];
        foreach ($xml->list->video as $video) {
            if (array_key_exists(base64_encode($param['cjurl'])."_".$video->tid,$vodtype) == false){
                continue;
            }
            $array_data[$key]['type_id'] = $vodtype[base64_encode($param['cjurl'])."_".$video->tid];
            $array_data[$key]['vod_id'] = (string)$video->id;
            //$array_data[$key]['type_id'] = (string)$video->tid;
            $array_data[$key]['vod_name'] = (string)$video->name;
            $array_data[$key]['vod_remarks'] = (string)$video->note;
            $array_data[$key]['type_name'] = (string)$video->type;
            $array_data[$key]['vod_pic'] = (string)$video->pic;
            $array_data[$key]['vod_lang'] = (string)$video->lang;
            $array_data[$key]['vod_area'] = (string)$video->area;
            $array_data[$key]['vod_year'] = (string)$video->year;
            $array_data[$key]['vod_serial'] = (string)$video->state;
            $array_data[$key]['vod_actor'] = (string)$video->actor;
            $array_data[$key]['vod_director'] = (string)$video->director;
            $array_data[$key]['vod_content'] = (string)$video->des;

            $array_data[$key]['vod_status'] = 1;
//            $array_data[$key]['vod_type'] = $array_data[$key]['list_name'];
            $array_data[$key]['vod_time'] = (string)$video->last;
            $array_data[$key]['vod_total'] = 0;
            $array_data[$key]['vod_isend'] = 1;
            if ($array_data[$key]['vod_serial']) {
                $array_data[$key]['vod_isend'] = 0;
            }
            //格式化地址与播放器
            $array_from = [];
            $array_url = [];
            $array_server = [];
            $array_note = [];
            //videolist|list播放列表不同
            if ($count = count($video->dl->dd)) {
                for ($i = 0; $i < $count; $i++) {
                    $array_from[$i] = (string)$video->dl->dd[$i]['flag'];
                    $array_url[$i] = $this->vod_xml_replace((string)$video->dl->dd[$i]);
                    $array_server[$i] = 'no';
                    $array_note[$i] = '';

                }
            } else {
                $array_from[] = (string)$video->dt;
                $array_url[] = '';
                $array_server[] = '';
                $array_note[] = '';
            }

            if (strpos(base64_decode($param['param']), 'ct=1') !== false) {
                $array_data[$key]['vod_down_from'] = implode('$$$', $array_from);
                $array_data[$key]['vod_down_url'] = implode('$$$', $array_url);
                $array_data[$key]['vod_down_server'] = implode('$$$', $array_server);
                $array_data[$key]['vod_down_note'] = implode('$$$', $array_note);
            } else {
                $array_data[$key]['vod_play_from'] = implode('$$$', $array_from);
                $array_data[$key]['vod_play_url'] = implode('$$$', $array_url);
                $array_data[$key]['vod_play_server'] = implode('$$$', $array_server);
                $array_data[$key]['vod_play_note'] = implode('$$$', $array_note);
            }

            $key++;
        }
        $array_type = [];
        $key=0;
        //分类列表
        if($param['ac'] == 'list'){
            foreach($xml->class->ty as $ty){
                $array_type[$key]['type_id'] = (string)$ty->attributes()->id;
                $array_type[$key]['type_name'] = (string)$ty;
                $key++;
            }
        }

        $mactype = DB::table("mac_type")->get();
        //如果有数据
        if(!$mactype->isEmpty()) {
            $mactype = $mactype->map(function ($value) {
                return (array)$value;
            })->toArray();
            $type_pid = array();
            foreach ($mactype as $item) {
                $type_pid[$item['type_id']] = $item['type_pid'];
            }
        }

        $res = ['code'=>1, 'msg'=>'xml', 'page'=>$array_page, 'type'=>$array_type, 'data'=>$array_data , 'type_pid'=>$type_pid];
        return $res;


    }
    public function vod_data($param,$data,$show=1)
    {
        if($show==1) {
            echo '当前采集任务<strong class="green">' . $data['page']['page'] . '</strong>/<span class="green">' . $data['page']['pagecount'] . '</span>页 采集地址&nbsp;' . $data['page']['url'] . '</br>';
        }
        $type_pid = $data['type_pid'];
        foreach($data['data'] as $k=>$v){
            $color='red';
            $des='';
            if($v['type_id'] ==0){
                $des = '分类未绑定，跳过err';
            }
            elseif(empty($v['vod_name'])) {
                $des = '数据不完整，跳过err';
            }
            else {
                unset($v['vod_id']);

                $v['type_id_1'] = intval($type_pid[$v['type_id']]);
//                $v['vod_en'] = Pinyin::get($v['vod_name']);
//                $v['vod_letter'] = strtoupper(substr($v['vod_en'],0,1));
                $v['vod_time_add'] = time();
                $v['vod_time'] = time();
//                $v['vod_status'] = $config['status'];

//                $v['vod_lock'] = intval($v['vod_lock']);
                $v['vod_status'] = intval($v['vod_status']);

                $v['vod_year'] = intval($v['vod_year']);
//                $v['vod_level'] = intval($v['vod_level']);
//                $v['vod_hits'] = intval($v['vod_hits']);
//                $v['vod_hits_day'] = intval($v['vod_hits_day']);
//                $v['vod_hits_week'] = intval($v['vod_hits_week']);
//                $v['vod_hits_month'] = intval($v['vod_hits_month']);
//                $v['vod_stint_play'] = intval($v['vod_stint_play']);
//                $v['vod_stint_down'] = intval($v['vod_stint_down']);

                $v['vod_total'] = intval($v['vod_total']);
                $v['vod_serial'] = intval($v['vod_serial']);
                $v['vod_isend'] = intval($v['vod_isend']);
//                $v['vod_up'] = intval($v['vod_up']);
//                $v['vod_down'] = intval($v['vod_down']);
//
//                $v['vod_score'] = floatval($v['vod_score']);
//                $v['vod_score_all'] = intval($v['vod_score_all']);
//                $v['vod_score_num'] = intval($v['vod_score_num']);

                $v['vod_class'] = $v['type_name'];
                unset($v['type_name']);
//
//                $v['vod_actor'] = mac_format_text($v['vod_actor']);
//                $v['vod_director'] = mac_format_text($v['vod_director']);
//                $v['vod_class'] = mac_format_text($v['vod_class']);
//                $v['vod_tag'] = mac_format_text($v['vod_tag']);

                if(empty($v['vod_isend']) && !empty($v['vod_serial'])){
                    $v['vod_isend'] = 0;
                }
                if(empty($v['vod_blurb'])){
                    $v['vod_blurb'] = $this->mac_substring( strip_tags($v['vod_content']) ,100);
                }

                $where = [];
                $where['vod_name'] = $v['vod_name'];
                $where['type_id'] = $v['type_id'];
                if(empty($v['vod_play_url'])){
                    $v['vod_play_url'] = '';
                }
                if(empty($v['vod_down_url'])){
                    $v['vod_down_url'] = '';
                }

                //验证地址
                if (strpos(base64_decode($param['param']), 'ct=1') !== false) {
                    $cj_down_from_arr = explode('$$$',$v['vod_down_from'] );
                    $cj_down_url_arr = explode('$$$',$v['vod_down_url']);
                    $cj_down_server_arr = explode('$$$',$v['vod_down_server']);
                    $cj_down_note_arr = explode('$$$',$v['vod_down_note']);

                    foreach($cj_down_from_arr as $kk=>$vv){
                        if(empty($vv)){
                            unset($cj_down_from_arr[$kk]);
                            continue;
                        }
                        $cj_down_url_arr[$kk] = rtrim($cj_down_url_arr[$kk]);
                        $cj_down_server_arr[$kk] = $cj_down_server_arr[$kk];
                        $cj_down_note_arr[$kk] = $cj_down_note_arr[$kk];
                    }
                    $v['vod_down_from'] = join('$$$',$cj_down_from_arr);
                    $v['vod_down_url'] = join('$$$',$cj_down_url_arr);
                    $v['vod_down_server'] = join('$$$',$cj_down_server_arr);
                    $v['vod_down_note'] = join('$$$',$cj_down_note_arr);

                }else{
                    $cj_play_from_arr = explode('$$$',$v['vod_play_from'] );
                    $cj_play_url_arr = explode('$$$',$v['vod_play_url']);
                    $cj_play_server_arr = explode('$$$',$v['vod_play_server']);
                    $cj_play_note_arr = explode('$$$',$v['vod_play_note']);
                    foreach($cj_play_from_arr as $kk=>$vv){
                        if(empty($vv)){
                            unset($cj_play_from_arr[$kk]);
                            continue;
                        }
                        $cj_play_url_arr[$kk] = rtrim($cj_play_url_arr[$kk],'#');
                        $cj_play_server_arr[$kk] = $cj_play_server_arr[$kk];
                        $cj_play_note_arr[$kk] = $cj_play_note_arr[$kk];
                    }
                    $v['vod_play_from'] = join('$$$',$cj_play_from_arr);
                    $v['vod_play_url'] = join('$$$',$cj_play_url_arr);
                    $v['vod_play_server'] = join('$$$',$cj_play_server_arr);
                    $v['vod_play_note'] = join('$$$',$cj_play_note_arr);

                }
                if(empty($v['vod_down_from'])) $v['vod_down_from']='';
                if(empty($v['vod_down_url'])) $v['vod_down_url']='';
                if(empty($v['vod_down_server'])) $v['vod_down_server']='';
                if(empty($v['vod_down_note'])) $v['vod_down_note']='';
                if(empty($v['vod_play_from'])) $v['vod_play_from']='';
                if(empty($v['vod_play_url'])) $v['vod_play_url']='';
                if(empty($v['vod_play_server'])) $v['vod_play_server']='';
                if(empty($v['vod_play_note'])) $v['vod_play_note']='';
                $info = DB::table("mac_vod")->where($where)->first();
                //如果有数据
                if($info == null) {
                    $res = DB::table("mac_vod")->insert($v);
                    if($res===false){
                        echo "插入失败!";
                    }
                    $color ='green';
                    $des= '新加入库，成功ok。';
                }else{
                    unset($v['vod_time_add']);
                    $info = $this->objectToArray($info);
                    $update = [];
                    $ec=false;
                    if (!empty($v['vod_play_from'])) {
                        $old_play_from = $info['vod_play_from'];
                        $old_play_url = $info['vod_play_url'];
                        $old_play_server = $info['vod_play_server'];
                        $old_play_note = $info['vod_play_note'];
                        foreach ($cj_play_from_arr as $k2 => $v2) {
                            $cj_play_from = $v2;
                            $cj_play_url = $cj_play_url_arr[$k2];
                            $cj_play_server = $cj_play_server_arr[$k2];
                            $cj_play_note = $cj_play_note_arr[$k2];
                            if ($cj_play_url == $info['vod_play_url']) {
                                $des .= '播放地址相同，跳过。';
                            } elseif (empty($cj_play_from)) {
                                $des .= '播放器类型为空，跳过。';
                            } elseif (strpos("," . $info['vod_play_from'], $cj_play_from) <= 0) {
                                $color = 'green';
                                $des .= '播放组(' . $cj_play_from . ')，新增ok。';
                                if(!empty($old_play_from)){
                                    $old_play_url .="$$$";
                                    $old_play_from .= "$$$" ;
                                    $old_play_server .= "$$$" ;
                                    $old_play_note .= "$$$" ;
                                }
                                $old_play_url .= "" . $cj_play_url;
                                $old_play_from .= "" . $cj_play_from;
                                $old_play_server .= "" . $cj_play_server;
                                $old_play_note .= "" . $cj_play_note;
                                $ec=true;
                            } else {
                                $arr1 = explode("$$$", $old_play_url);
                                $arr2 = explode("$$$", $old_play_from);
                                $play_key = array_search($cj_play_from, $arr2);
                                if ($arr1[$play_key] == $cj_play_url) {
                                    $des .= '播放组(' . $cj_play_from . ')，无需更新。';
                                } else {
                                    $color = 'green';
                                    $des .= '播放组(' . $cj_play_from . ')，更新ok。';
                                    $arr1[$play_key] = $cj_play_url;
                                    $ec=true;
                                }
                                $old_play_url = join('$$$', $arr1);
                            }
                        }
                        if($ec) {
                            $update['vod_play_from'] = $old_play_from;
                            $update['vod_play_url'] = $old_play_url;
                            $update['vod_play_server'] = $old_play_server;
                            $update['vod_play_note'] = $old_play_note;
                        }
                    }
                    $ec=false;
                    if (!empty($v['vod_down_from'])) {
                        $old_down_from = $info['vod_down_from'];
                        $old_down_url = $info['vod_down_url'];
                        $old_down_server = $info['vod_down_server'];
                        $old_down_note = $info['vod_down_note'];

                        foreach ($cj_down_from_arr as $k2 => $v2) {
                            $cj_down_from = $v2;
                            $cj_down_url = $cj_down_url_arr[$k2];
                            $cj_down_server = $cj_down_server_arr[$k2];
                            $cj_down_note = $cj_down_note_arr[$k2];


                            if ($cj_down_url == $info['vod_down_url']) {
                                $des .= '下载地址相同，跳过。';
                            } elseif (empty($cj_down_from)) {
                                $des .= '下载器类型为空，跳过。';
                            } elseif (strpos("," . $info['vod_down_from'], $cj_down_from)===false) {
                                $color = 'green';
                                $des .= '下载组(' . $cj_down_from . ')，新增ok。';
                                if(!empty($old_down_from)){
                                    $old_down_url .="$$$";
                                    $old_down_from .= "$$$" ;
                                    $old_down_server .= "$$$" ;
                                    $old_down_note .= "$$$" ;
                                }

                                $old_down_url .= $cj_down_url;
                                $old_down_from .= $cj_down_from;
                                $old_down_server .= $cj_down_server;
                                $old_down_note .= $cj_down_note;
                                $ec=true;
                            } else {
                                $arr1 = explode("$$$", $old_down_url);
                                $arr2 = explode("$$$", $old_down_from);
                                $down_key = array_search($cj_down_from, $arr2);
                                if ($arr1[$down_key] == $cj_down_url) {
                                    $des .= '下载组(' . $cj_down_from . ')，无需更新。';
                                } else {
                                    $color = 'green';
                                    $des .= '下载组(' . $cj_down_from . ')，更新ok。';
                                    $arr1[$down_key] = $cj_down_url;
                                    $ec=true;
                                }
                                $old_down_url = join('$$$', $arr1);
                            }
                        }

                        if($ec) {
                            $update['vod_down_from'] = $old_down_from;
                            $update['vod_down_url'] = $old_down_url;
                            $update['vod_down_server'] = $old_down_server;
                            $update['vod_down_note'] = $old_down_note;
                        }
                    }
                    $update['vod_serial'] = $v['vod_serial'];
                    $update['vod_remarks'] = $v['vod_remarks'];
                    $update['vod_class'] = $v['vod_class'];
                    $update['type_id']=$v['type_id'];
                    $update['type_id_1']=$v['type_id_1'];
                    if (count($update) > 0) {
                        $update['vod_time'] = time();

                        $where = [];
                        $where['vod_id'] = $info['vod_id'];
                        $res = DB::table("mac_vod")->where($where)->update($update);
                        if ($res === false) {
                            echo "更新失败!";
                        }
                    }
                }
            }
            if($show==1) {
                echo ($k + 1) .'、'. $v['vod_name'] . "<font color=$color>" .$des .'</font>'.'</br>' ;
            }
            else{
                return ['code'=>($color=='red' ? 1001 : 1),'msg'=>$des ];
            }
        }
        if ($data['page']['page'] < $data['page']['pagecount']) {
            $param['page'] = intval($data['page']['page']) + 1;
            $res = $this->vod($param);
            if($res['code']>1){
                echo $res['msg'];
                return;
            }
            ob_flush();
            flush();
            $this->vod_data($param,$res );
        }
        echo "数据采集完成</br>";
        die;
    }
    public function vod_xml_replace($url)
    {
        $array_url = array();
        $arr_ji = explode('#',str_replace('||','//',$url));
        foreach($arr_ji as $key=>$value){
            $urlji = explode('$',$value);
            if( count($urlji) > 1 ){
                $array_url[$key] = $urlji[0].'$'.trim($urlji[1]);
            }else{
                $array_url[$key] = trim($urlji[0]);
            }
        }
        return implode('#',$array_url);
    }
    function mac_substring($str, $lenth, $start=0)
    {
        $len = strlen($str);
        $r = array();
        $n = 0;
        $m = 0;

        for($i=0;$i<$len;$i++){
            $x = substr($str, $i, 1);
            $a = base_convert(ord($x), 10, 2);
            $a = substr( '00000000 '.$a, -8);

            if ($n < $start){
                if (substr($a, 0, 1) == 0) {
                }
                else if (substr($a, 0, 3) == 110) {
                    $i += 1;
                }
                else if (substr($a, 0, 4) == 1110) {
                    $i += 2;
                }
                $n++;
            }
            else{
                if (substr($a, 0, 1) == 0) {
                    $r[] = substr($str, $i, 1);
                }else if (substr($a, 0, 3) == 110) {
                    $r[] = substr($str, $i, 2);
                    $i += 1;
                }else if (substr($a, 0, 4) == 1110) {
                    $r[] = substr($str, $i, 3);
                    $i += 2;
                }else{
                    $r[] = ' ';
                }
                if (++$m >= $lenth){
                    break;
                }
            }
        }
        return  join('',$r);
    }

    // CurlPOST数据提交-----------------------------------------
    function mac_curl_get($url,$heads=array(),$cookie='')
    {
        set_time_limit(0);
        $ch = @curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36');

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        if(!empty($cookie)){
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if(count($heads)>0){
            curl_setopt ($ch, CURLOPT_HTTPHEADER , $heads );
        }
        $response = @curl_exec($ch);
        if(curl_errno($ch)){//出错则显示错误信息
            echo curl_error($ch);die;
        }
        curl_close($ch); //关闭curl链接
        return $response;//显示返回信息
    }

    /// /////////////////////采集 eng ////////////////////
    /// /////////////////////采集页面begin////////////////
    public function searchCollect(){
        $vodconfig = config('vodconfig');
        $ruleplayer =$this->mac_get_rule_player();
        $macvod = DB::table("mac_vod")->where([
            ['vod_class', 'not regexp', $vodconfig['ruleout']],
            ['type_id_1','=',1]
        ])->where([['vod_play_from','regexp',$ruleplayer]])->orderBy("vod_time","desc")->limit(20)->get();
        $search_list = array();
        //如果有数据
        if(!$macvod->isEmpty()) {
            $macvod = $macvod->map(function ($value) {
                return (array)$value;
            })->toArray();
            $key = 0;
            foreach ($macvod as $item) {
                if($this->mac_play_list_show($item['vod_play_from']) == false){
                    continue;
                }
                $search_list[$key]['vod_id'] = base64_encode($item['vod_id']);
                $search_list[$key]['vod_pic'] = $item['vod_pic'];
                $search_list[$key]['vod_name'] = $item['vod_name'];
                $search_list[$key]['vod_remarks'] = $item['vod_remarks'];
                $search_list[$key]['vod_score'] = $item['vod_score'];
                $search_list[$key]['vod_serial'] = $item['vod_serial'];
                $search_list[$key]['vod_class'] = $item['vod_class'];
                $key++;
            }
        }

        $search_list = $this->arrayToObject($search_list);
        return view('searchCollect', ['search_list' => $search_list]);
    }
    public function searchCollectMore(Request $request){
        if($request->has("nowPage"))
        {
            $nowPage = $request->input("nowPage");
        }
        else{
            $nowPage = 1;
        }
        if($request->has("m"))
        {
            $typeid = $request->input("m");
        }
        else{
            $typeid = 999;
        }
        if($request->has("player"))
        {
            $player = $request->input("player");
        }
        else{
            $player = "";
        }
        $pageNum = 60;
        $vodconfig = config('vodconfig');
        if($typeid == 999){
            $where = [
                ['vod_class', 'not regexp', $vodconfig['ruleout']],
                ['type_id_1','=',1]
            ];
        }else{
            $where = [
                ['vod_class', 'not regexp', $vodconfig['ruleout']],
                ['type_id_1','=',1],
                ['type_id','=',$typeid]
            ];
        }
        $ruleplayer =$this->mac_get_rule_player($player);
        $macvod = DB::table("mac_vod")->where($where)->where([['vod_play_from','regexp',$ruleplayer]])->orderBy("vod_time","desc")->forPage($nowPage, $pageNum)->get();
        $search_list = array();
        //如果有数据
        if(!$macvod->isEmpty()) {
            $macvod = $macvod->map(function ($value) {
                return (array)$value;
            })->toArray();
            $key = 0;
            foreach ($macvod as $item) {
                if($this->mac_play_list_show($item['vod_play_from']) == false){
                    continue;
                }
                $search_list[$key]['vod_id'] = base64_encode($item['vod_id']);
                $search_list[$key]['vod_pic'] = $item['vod_pic'];
                $search_list[$key]['vod_name'] = $item['vod_name'];
                $search_list[$key]['vod_remarks'] = $item['vod_remarks'];
                $search_list[$key]['vod_score'] = $item['vod_score'];
                $search_list[$key]['vod_serial'] = $item['vod_serial'];
                $search_list[$key]['vod_class'] = $item['vod_class'];
                $key++;
            }
        }

        $count = DB::table("mac_vod")->where($where)->where([['vod_play_from','regexp',$ruleplayer]])->count();
        $countPage = ceil($count / $pageNum);
        $search = array();
        $search['m'] = $typeid;
        $search['player'] = $player;
        $pages = CustomPage::getSelfPageView($nowPage, $countPage, '/movie', $search);
        $search_list = $this->arrayToObject($search_list);

        $mactype = DB::table("mac_type")->where([
            ['type_pid','=',1]
        ])->orderBy("type_sort")->get();
        //如果有数据
        $type_list = array();
        if(!$mactype->isEmpty()) {
            $mactype = $mactype->map(function ($value) {
                return (array)$value;
            })->toArray();
            foreach ($mactype as $key =>$item) {
                $type_list[$key]['type_id'] = $item['type_id'];
                $type_list[$key]['type_name'] = $item['type_name'];
            }
        }
        $type_list = $this->arrayToObject($type_list);
        $all_player = $this->mac_get_all_player();

        return view('searchCollectMore', ['search_list' => $search_list,'pages'=>$pages,'type_list'=>$type_list,'all_player'=>$all_player,'typeid'=>$typeid,'player'=>$player]);
    }

    public function searchCollectTv(Request $request){
        if($request->has("nowPage"))
        {
            $nowPage = $request->input("nowPage");
        }
        else{
            $nowPage = 1;
        }
        if($request->has("m"))
        {
            $typeid = $request->input("m");
        }
        else{
            $typeid = 999;
        }
        if($request->has("player"))
        {
            $player = $request->input("player");
        }
        else{
            $player = "";
        }
        $pageNum = 60;
        $vodconfig = config('vodconfig');
        if($typeid == 999){
            $where = [
                ['vod_class', 'not regexp', $vodconfig['ruleout']],
                ['type_id_1','=',2]
            ];
        }else{
            $where = [
                ['vod_class', 'not regexp', $vodconfig['ruleout']],
                ['type_id_1','=',2],
                ['type_id','=',$typeid]
            ];
        }
        $ruleplayer =$this->mac_get_rule_player($player);
        $macvod = DB::table("mac_vod")->where($where)->where([['vod_play_from','regexp',$ruleplayer]])->orderBy("vod_time","desc")->forPage($nowPage, $pageNum)->get();
        $search_list = array();
        //如果有数据
        if(!$macvod->isEmpty()) {
            $macvod = $macvod->map(function ($value) {
                return (array)$value;
            })->toArray();
            $key = 0;
            foreach ($macvod as $item) {
                if($this->mac_play_list_show($item['vod_play_from']) == false){
                    continue;
                }
                $search_list[$key]['vod_id'] = base64_encode($item['vod_id']);
                $search_list[$key]['vod_pic'] = $item['vod_pic'];
                $search_list[$key]['vod_name'] = $item['vod_name'];
                $search_list[$key]['vod_remarks'] = $item['vod_remarks'];
                $search_list[$key]['vod_score'] = $item['vod_score'];
                $search_list[$key]['vod_serial'] = $item['vod_serial'];
                $search_list[$key]['vod_class'] = $item['vod_class'];
                $key++;
            }
        }

        $count = DB::table("mac_vod")->where($where)->where([['vod_play_from','regexp',$ruleplayer]])->count();
        $countPage = ceil($count / $pageNum);
        $search = array();
        $search['m'] = $typeid;
        $search['player'] = $player;
        $pages = CustomPage::getSelfPageView($nowPage, $countPage, '/tv', $search);
        $search_list = $this->arrayToObject($search_list);

        $mactype = DB::table("mac_type")->where([
            ['type_pid','=',2]
        ])->orderBy("type_sort")->get();
        //如果有数据
        $type_list = array();
        if(!$mactype->isEmpty()) {
            $mactype = $mactype->map(function ($value) {
                return (array)$value;
            })->toArray();
            foreach ($mactype as $key =>$item) {
                $type_list[$key]['type_id'] = $item['type_id'];
                $type_list[$key]['type_name'] = $item['type_name'];
            }
        }
        $type_list = $this->arrayToObject($type_list);
        $all_player = $this->mac_get_all_player();
        return view('searchCollectTv', ['search_list' => $search_list,'pages'=>$pages,'type_list'=>$type_list,'all_player'=>$all_player,'typeid'=>$typeid,'player'=>$player]);
    }

    public function searchCollectDongman(Request $request){
        if($request->has("nowPage"))
        {
            $nowPage = $request->input("nowPage");
        }
        else{
            $nowPage = 1;
        }
        if($request->has("m"))
        {
            $typeid = $request->input("m");
        }
        else{
            $typeid = 999;
        }
        if($request->has("player"))
        {
            $player = $request->input("player");
        }
        else{
            $player = "";
        }
        $pageNum = 60;
        $vodconfig = config('vodconfig');
        if($typeid == 999){
            $where = [
                ['vod_class', 'not regexp', $vodconfig['ruleout']],
                ['type_id_1','=',4]
            ];
        }else{
            $where = [
                ['vod_class', 'not regexp', $vodconfig['ruleout']],
                ['type_id_1','=',4],
                ['type_id','=',$typeid]
            ];
        }
        $ruleplayer =$this->mac_get_rule_player($player);
        $macvod = DB::table("mac_vod")->where($where)->where([['vod_play_from','regexp',$ruleplayer]])->orderBy("vod_time","desc")->forPage($nowPage, $pageNum)->get();
        $search_list = array();
        //如果有数据
        if(!$macvod->isEmpty()) {
            $macvod = $macvod->map(function ($value) {
                return (array)$value;
            })->toArray();
            $key = 0;
            foreach ($macvod as $item) {
                if($this->mac_play_list_show($item['vod_play_from']) == false){
                    continue;
                }
                $search_list[$key]['vod_id'] = base64_encode($item['vod_id']);
                $search_list[$key]['vod_pic'] = $item['vod_pic'];
                $search_list[$key]['vod_name'] = $item['vod_name'];
                $search_list[$key]['vod_remarks'] = $item['vod_remarks'];
                $search_list[$key]['vod_score'] = $item['vod_score'];
                $search_list[$key]['vod_serial'] = $item['vod_serial'];
                $search_list[$key]['vod_class'] = $item['vod_class'];
                $key++;
            }
        }

        $count = DB::table("mac_vod")->where($where)->where([['vod_play_from','regexp',$ruleplayer]])->count();
        $countPage = ceil($count / $pageNum);
        $search = array();
        $search['m'] = $typeid;
        $search['player'] = $player;
        $pages = CustomPage::getSelfPageView($nowPage, $countPage, '/dongman', $search);
        $search_list = $this->arrayToObject($search_list);

        $mactype = DB::table("mac_type")->where([
            ['type_pid','=',4]
        ])->orderBy("type_sort")->get();
        $type_list = array();
        //如果有数据
        if(!$mactype->isEmpty()) {
            $mactype = $mactype->map(function ($value) {
                return (array)$value;
            })->toArray();
            foreach ($mactype as $key =>$item) {
                $type_list[$key]['type_id'] = $item['type_id'];
                $type_list[$key]['type_name'] = $item['type_name'];
            }
        }
        $type_list = $this->arrayToObject($type_list);
        $all_player = $this->mac_get_all_player();
        return view('searchCollectDongman', ['search_list' => $search_list,'pages'=>$pages,'type_list'=>$type_list,'all_player'=>$all_player,'typeid'=>$typeid,'player'=>$player]);
    }

    public function searchCollectZongyi(Request $request){
        if($request->has("nowPage"))
        {
            $nowPage = $request->input("nowPage");
        }
        else{
            $nowPage = 1;
        }
        if($request->has("m"))
        {
            $typeid = $request->input("m");
        }
        else{
            $typeid = 999;
        }
        if($request->has("player"))
        {
            $player = $request->input("player");
        }
        else{
            $player = "";
        }
        $pageNum = 60;
        $vodconfig = config('vodconfig');
        if($typeid == 999){
            $where = [
                ['vod_class', 'not regexp', $vodconfig['ruleout']],
                ['type_id_1','=',3]
            ];
        }else{
            $where = [
                ['vod_class', 'not regexp', $vodconfig['ruleout']],
                ['type_id_1','=',3],
                ['type_id','=',$typeid]
            ];
        }
        $ruleplayer =$this->mac_get_rule_player($player);
        $macvod = DB::table("mac_vod")->where($where)->where([['vod_play_from','regexp',$ruleplayer]])->orderBy("vod_time","desc")->forPage($nowPage, $pageNum)->get();
        $search_list = array();
        //如果有数据
        if(!$macvod->isEmpty()) {
            $macvod = $macvod->map(function ($value) {
                return (array)$value;
            })->toArray();
            $key = 0;
            foreach ($macvod as $item) {
                if($this->mac_play_list_show($item['vod_play_from']) == false){
                    continue;
                }
                $search_list[$key]['vod_id'] = base64_encode($item['vod_id']);
                $search_list[$key]['vod_pic'] = $item['vod_pic'];
                $search_list[$key]['vod_name'] = $item['vod_name'];
                $search_list[$key]['vod_remarks'] = $item['vod_remarks'];
                $search_list[$key]['vod_score'] = $item['vod_score'];
                $search_list[$key]['vod_serial'] = $item['vod_serial'];
                $search_list[$key]['vod_class'] = $item['vod_class'];
                $key++;
            }
        }

        $count = DB::table("mac_vod")->where($where)->where([['vod_play_from','regexp',$ruleplayer]])->count();
        $countPage = ceil($count / $pageNum);
        $search = array();
        $search['m'] = $typeid;
        $search['player'] = $player;
        $pages = CustomPage::getSelfPageView($nowPage, $countPage, '/zongyi', $search);
        $search_list = $this->arrayToObject($search_list);

        $mactype = DB::table("mac_type")->where([
            ['type_pid','=',3]
        ])->orderBy("type_sort")->get();
        $type_list = array();
        //如果有数据
        if(!$mactype->isEmpty()) {
            $mactype = $mactype->map(function ($value) {
                return (array)$value;
            })->toArray();
            foreach ($mactype as $key =>$item) {
                $type_list[$key]['type_id'] = $item['type_id'];
                $type_list[$key]['type_name'] = $item['type_name'];
            }
        }
        $type_list = $this->arrayToObject($type_list);
        $all_player = $this->mac_get_all_player();
        return view('searchCollectZongyi', ['search_list' => $search_list,'pages'=>$pages,'type_list'=>$type_list,'all_player'=>$all_player,'typeid'=>$typeid,'player'=>$player]);
    }
    public function searchCollectMeiju(Request $request){
        if($request->has("nowPage"))
        {
            $nowPage = $request->input("nowPage");
        }
        else{
            $nowPage = 1;
        }
        if($request->has("m"))
        {
            $typeid = $request->input("m");
        }
        else{
            $typeid = 999;
        }
        if($request->has("player"))
        {
            $player = $request->input("player");
        }
        else{
            $player = "";
        }
        $pageNum = 60;
        $vodconfig = config('vodconfig');
        if($typeid == 999){
            $where = [
                ['vod_class', 'not regexp', $vodconfig['ruleout']],
                ['type_id_1','=',6]
            ];
        }else{
            $where = [
                ['vod_class', 'not regexp', $vodconfig['ruleout']],
                ['type_id_1','=',6],
                ['type_id','=',$typeid]
            ];
        }
        $ruleplayer =$this->mac_get_rule_player($player);
        $macvod = DB::table("mac_vod")->where($where)->where([['vod_play_from','regexp',$ruleplayer]])->orderBy("vod_time","desc")->forPage($nowPage, $pageNum)->get();
        $search_list = array();
        //如果有数据
        if(!$macvod->isEmpty()) {
            $macvod = $macvod->map(function ($value) {
                return (array)$value;
            })->toArray();
            $key = 0;
            foreach ($macvod as $item) {
                if($this->mac_play_list_show($item['vod_play_from']) == false){
                    continue;
                }
                $search_list[$key]['vod_id'] = base64_encode($item['vod_id']);
                $search_list[$key]['vod_pic'] = $item['vod_pic'];
                $search_list[$key]['vod_name'] = $item['vod_name'];
                $search_list[$key]['vod_remarks'] = $item['vod_remarks'];
                $search_list[$key]['vod_score'] = $item['vod_score'];
                $search_list[$key]['vod_serial'] = $item['vod_serial'];
                $search_list[$key]['vod_class'] = $item['vod_class'];
                $key++;
            }
        }

        $count = DB::table("mac_vod")->where($where)->where([['vod_play_from','regexp',$ruleplayer]])->count();
        $countPage = ceil($count / $pageNum);
        $search = array();
        $search['m'] = $typeid;
        $search['player'] = $player;
        $pages = CustomPage::getSelfPageView($nowPage, $countPage, '/meiju', $search);
        $search_list = $this->arrayToObject($search_list);

        $mactype = DB::table("mac_type")->where([
            ['type_pid','=',6]
        ])->orderBy("type_sort")->get();
        $type_list = array();
        //如果有数据
        if(!$mactype->isEmpty()) {
            $mactype = $mactype->map(function ($value) {
                return (array)$value;
            })->toArray();
            foreach ($mactype as $key =>$item) {
                $type_list[$key]['type_id'] = $item['type_id'];
                $type_list[$key]['type_name'] = $item['type_name'];
            }
        }
        $type_list = $this->arrayToObject($type_list);
        $all_player = $this->mac_get_all_player();
        return view('searchCollectMeiju', ['search_list' => $search_list,'pages'=>$pages,'type_list'=>$type_list,'all_player'=>$all_player,'typeid'=>$typeid,'player'=>$player]);
    }

    //搜索
    public function searchCollectResult(Request $request){
        if($request->has("nowPage"))
        {
            $nowPage = $request->input("nowPage");
        }
        else{
            $nowPage = 1;
        }
        if($request->has("player"))
        {
            $player = $request->input("player");
        }
        else{
            $player = "";
        }


        $pageNum = 60;
        $search_list = array();
        if($request->has('mov_name')){
            $mov_name = $request->input('mov_name');
            //记录搜索热度
            $this->DBHotSearch($mov_name,'collect');
            $start = substr($mov_name,0,3);

            $ruleplayer =$this->mac_get_rule_player($player);
            $vodconfig = config('vodconfig');
            $where = [
                ['vod_name', 'like', '%'.$mov_name.'%'],
                ['vod_class', 'not regexp', $vodconfig['ruleout']]
            ];

            $macvod = DB::table("mac_vod")->where($where)->where([['vod_play_from','regexp',$ruleplayer]])->orderBy("vod_time","desc")->forPage($nowPage, $pageNum)->get();
            //如果有数据
            if(!$macvod->isEmpty()) {
                $macvod = $macvod->map(function ($value) {
                    return (array)$value;
                })->toArray();
                $key = 0;
                foreach ($macvod as $item) {
                    if($start != "@!#" && $this->mac_play_list_show($item['vod_play_from']) == false){
                        continue;
                    }
                    $search_list[$key]['vod_id'] = base64_encode($item['vod_id']);
                    $search_list[$key]['vod_pic'] = $item['vod_pic'];
                    $search_list[$key]['vod_name'] = $item['vod_name'];
                    $search_list[$key]['vod_remarks'] = $item['vod_remarks'];
                    $search_list[$key]['vod_score'] = $item['vod_score'];
                    $search_list[$key]['vod_serial'] = $item['vod_serial'];
                    $search_list[$key]['vod_class'] = $item['vod_class'];
                    $key++;
                }
            }
            $count = DB::table("mac_vod")->where($where)->where([['vod_play_from','regexp',$ruleplayer]])->count();
            $countPage = ceil($count / $pageNum);
            $search = array();
            $search['mov_name'] = $mov_name;
            $search['player'] = $player;
            $pages = CustomPage::getSelfPageView($nowPage, $countPage, '/searchCollectResult', $search);
            $search_list = $this->arrayToObject($search_list);
            $all_player = $this->mac_get_all_player();
            return view('searchCollectResult', ['search_list' => $search_list, 'search_name' => $mov_name,'pages'=>$pages,'all_player'=>$all_player,'player'=>$player]);
        }
    }
    //播放
    public  function searchCollectPlay(Request $request){
        $item=array();
        $player=base64_decode($request->input('play'));
        $macvod = DB::table("mac_vod")->where([
            ['vod_id', '=', $player]
        ])->first();
        if($macvod != null){
            $macvod = $this->objectToArray($macvod);
            $vod_name = $macvod['vod_name'];
            $vod_content = $macvod['vod_content'];
            $vod_play_list = [];
            $vod_down_list = [];
            if (!empty($macvod['vod_play_from'])) {
                $vod_play_list = $this->mac_play_list($macvod['vod_play_from'],$macvod['vod_play_url'],$macvod['vod_play_server'],$macvod['vod_play_note'],'play');
            }
//            if (!empty($macvod['vod_down_from'])) {
//                $vod_down_list = $this->mac_play_list($macvod['vod_down_from'], $macvod['vod_down_url'], $macvod['vod_down_server'], $macvod['vod_down_note'], 'down');
//            }
            $item = array_merge($vod_play_list,$vod_down_list);
//            var_dump($item);
        }
        $play_item=$this->arrayToObject($item);
        return view('searchCollectPlay',['play_item'=>$play_item,'vod_name'=>$vod_name,'vod_content'=>$vod_content]);
    }
    function mac_play_list($vod_play_from,$vod_play_url,$vod_play_server,$vod_play_note,$flag='play')
    {
        $vod_play_from_list = [];
        $vod_play_url_list = [];
        $vod_play_server_list = [];
        $vod_play_note_list = [];

        $player_list = config('vodplayer');
        if(!empty($vod_play_from)) {
            $vod_play_from_list = explode('$$$', $vod_play_from);
        }
        if(!empty($vod_play_url)) {
            $vod_play_url_list = explode('$$$', $vod_play_url);
        }
        if(!empty($vod_play_server)) {
            $vod_play_server_list = explode('$$$', $vod_play_server);
        }
        if(!empty($vod_play_note)) {
            $vod_play_note_list = explode('$$$', $vod_play_note);
        }

        $res_list = [];

        foreach($vod_play_from_list as $k=>$v){
            $player_info = array_key_exists($v,$player_list)?$player_list[$v]:$player_list['default'];
            if($player_info['status'] == '0' && $v != "ckplayer"){
                continue;
            }
            $server = (string)$vod_play_server_list[$k];
            $urls = $this->mac_play_list_one($vod_play_url_list[$k],$v);

            $sort[] = $player_info['sort'];
            $res_list[$k] = [
                'sid'=> $k,
                'player_info'=> $player_info,
                'server_info'=> '' ,
                'from'=>$v,
                'url'=>$vod_play_url_list[$k],
                'server'=>$server,
                'note'=>$vod_play_note_list?$vod_play_note_list[$k]:'',
                'url_count'=> count($urls),
                'urls'=> $urls,
            ];
            array_multisort($sort, SORT_ASC, SORT_STRING, $res_list);
        }
//        if(count($res_list)==0 || $flag=='down'){
//            foreach($vod_play_from_list as $k=>$v){
//                $server = (string)$vod_play_server_list[$k];
//                $urls = $this->mac_play_list_one($vod_play_url_list[$k],$v);
//
//                $player_info = $player_list[$v];
//                $sort[] = $player_info['sort'];
//                $res_list[$k] = [
//                    'sid'=> $k,
//                    'player_info'=> $player_info,
//                    'server_info'=> '' ,
//                    'from'=>$v,
//                    'url'=>$vod_play_url_list[$k],
//                    'server'=>$server,
//                    'note'=>$vod_play_note_list?$vod_play_note_list[$k]:'',
//                    'url_count'=> count($urls),
//                    'urls'=> $urls,
//                ];
//                array_multisort($sort, SORT_ASC, SORT_STRING, $res_list);
//            }
//        }
        return $res_list;
    }
    function mac_play_list_one($url_one, $from_one, $server_one=''){
        $url_list = array();
        $array_url = explode('#',$url_one);
        foreach($array_url as $key=>$val){
            if(strpos($val,"$") === false){
                $url_list[$key]['name'] = "BD";
                $url_list[$key]['url'] = $server_one.$val;
            }else{
                list($title, $url) = explode('$', $val);
                if ( empty($url) ) {
                    $url_list[$key]['name'] = '第'.($key+1).'集';
                    $url_list[$key]['url'] = $server_one.$title;
                }else{
                    $url_list[$key]['name'] = $title;
                    $url_list[$key]['url'] = $server_one.$url;
                }
            }
            $from = $from_one;
            $url_list[$key]['from'] = (string)$from;
            $url_list[$key]['nid'] = $key+1;
        }
        return $url_list;
    }
    function mac_play_list_show($vod_play_from){
        $player_list = config('vodplayer');
        foreach ($player_list as $key=>$item){
            if($item['status'] == '1' && strpos($vod_play_from,$key) !== false){
                return true;
            }
        }
        return false;
    }
    function mac_get_rule_player($player=""){
        $player_list = config('vodplayer');
        $ruleplayer ="";
        foreach ($player_list as $key=>$item){
            if($item['status'] == '1'){
                if($player != ""){
                    if($player == $key){
                        $ruleplayer = $key;
                        break;
                    }else{
                        continue;
                    }
                }
                if($ruleplayer == "") {
                    $ruleplayer = $key;
                }else{
                    $ruleplayer = $ruleplayer."|".$key;
                }
            }
        }
        return $ruleplayer;
    }
    function mac_get_all_player(){
        $player_list = config('vodplayer');
        $result = array();
        $i = 0;
        foreach ($player_list as $key=>$item){
            if($item['status'] == '1' && $key != 'default'){
                $result[$i]['player'] = $key;
                $result[$i]['player_name'] = $item['show'];
                $i = $i +1;
                $sort[] = $item['sort'];
                array_multisort($sort, SORT_ASC, SORT_STRING, $result);
            }
        }

        $result = $this->arrayToObject($result);
        return $result;
    }

    /// /////////////////////采集页面end//////////////////
    //数组转对象
    public function arrayToObject($e)
    {

        if (gettype($e) != 'array') return;
        foreach ($e as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object')
                $e[$k] = (object)$this->arrayToObject($v);
        }
        return (object)$e;
    }
    /**
     * 对象转换数组
     *
     * @param $e StdClass对象实例
     * @return array|void
     */
    public function objectToArray($e)
    {
        $e = (array)$e;
        foreach ($e as $k => $v) {
            if (gettype($v) == 'resource') return;
            if (gettype($v) == 'object' || gettype($v) == 'array')
                $e[$k] = (array)$this->objectToArray($v);
        }
        return $e;
    }
    //------------------------数据库操作 begin -----------------------------
    //搜索热度数据库操作
    public function DBHotSearch($mov_name,$engine){
        date_default_timezone_set("Asia/Shanghai");
        $hot = DB::table("searches")->where([
            ['name', '=', $engine.':'.$mov_name]
        ])->first();
        if($hot == null){
            DB::table("searches")->insert([
                "name"=>$engine.':'.$mov_name,
                "time"=>date('y-m-d H:i:s',time()),
                "total"=>1
            ]);
        }else{
            $total = ($hot->total) + 1;
            DB::table("searches")->where([
                ['name', '=', $engine.':'.$mov_name]
            ])->update(['total'=>$total, 'time'=>date('y-m-d H:i:s',time())]);
        }
    }
    //------------------------数据库操作 end---------------------------------




}