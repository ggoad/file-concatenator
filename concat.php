<?php 
// check for submission
if($_POST['dat'] ?? false){
   $dat=json_decode($_POST['dat'],true);
   
   // state variables
   $conc=[];
   $err=[];
   
   
   foreach($dat['list'] as $l)
   {
	   // send a true http request if $l has a protocol
	   // 	otherwise, just a normal file_get_contents
		if(preg_match('#^http#',$l)){
		  $opts=[
			'http'=>[
				'method'=>'GET',
				'header'=>"User-Agent: ConcUA\r\n",
				'timeout'=>120,
				'follow_location'=>false
			]
		  ];
		  $context=stream_context_create($opts);
		  $txt=file_get_contents($l, false, $context);
		}else{
			$txt=file_get_contents($l);
		}
	  
	  // report failure
      if(!$txt){$txt="/* FAILED */"; $err[]="$l Failed<br>";}
	  
	  // add to output
      $conc[]="/* START $l */";
      $conc[]=$txt;
      $conc[]="/* END $l */";
      $conc[]='';
   }
   $conc=join("\n", $conc);
   
   // detect file name
   $fileName=$dat['fileName'] ?: '';
   if($fileName){
  	 file_put_contents($fileName, $conc);
   }   
}
?>
<!DOCTYPE html>
<HTML id="HTML" lang="en-US" >
<HEAD id="HEAD">
<title id="TITLE">Auto Concatenate</title>
<meta name="resource-type" content="document" >
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" >
<meta name="pagename" content="Auto Concatenate" >
<meta name="viewport" content="width=device-width, initial-scale=1" >
<meta name="author" content="Greg Goad" >
<meta name="language" content="English" >
<meta name="designer" content="Greg Goad" >
<meta name="HandheldFriendly" content="True" >
<meta name="MobileOptimized" content="True" >
<meta name="target" content="_top" >
<meta name="DC.title" content="Auto Concatenate" >
<meta name="DC.creator" content="Greg Goad" >
<meta property="og:title" content="Auto Concatenate" >
<meta property="og:type" content="website" >
<meta property="og:locale" content="en_US" >

<script id='js_library/ob2'>
/* START _object */
_ob={
	/* this is a legacy function. A long time ago, this was how you 'did' inheritance in JS */
	COPY_proto:(function(){
		function Temp(){}
		return function(O){
			if(typeof O != "object"){
				throw TypeError("Object prototype may only be an Object or null");
			}
			Temp.prototype=O;
			var obj=new Temp();
			Temp.prototype=null;
			return obj;
		};
	})(),
	/* combine 2 object into a new object */
	COMBINE:function(ob1, ob2){
		ob1=ob1 || {};
		ob2=ob2 || {};
		var ret={};
		this.INSERT(ret, ob1);
		this.INSERT(ret, ob2);
		return ret;
	},
	/* insert members into a reciever object */
	INSERT:function(reciever, con){
		con = con || {};
		for(var mem in con)
		{
			reciever[mem]=con[mem];
		}
	},
	/*  this could probably be removed */
	PARSE_default:function(def,set){
	   return this.COMBINE(def,set);
	},
	/* compare two objects, key by key */
	COMPARE:function(ob1, ob2){
		if(typeof ob1 !== "object" || typeof ob2 !== "object"){return false;}
		if(Object.keys(ob1).length !== Object.keys(ob2).length){return false;}
		var cmp=true;
		for(var mem in ob1)
		{
		   if(typeof ob1[mem] === "object"){
			  cmp=this.COMPARE(ob1[mem], ob2[mem]);
		   }else{
			  cmp=(ob1[mem] === ob2[mem]);
		   }
		   if(!cmp){return false;}
		}
		return true;
	},
	/* a control to set how deep clone will run recursively */
	CLONE_depthLimit:20,
	/* clone an object */
	CLONE:(function(){
		return function(obj, depth, callDepth){
			depth=depth || 1;
			callDepth=callDepth || 0;
			if(depth === -1){
				depth = this.CLONE_depthLimit;
				if(callDepth === this.CLONE_depthLimit){
					throw new TypeError("Depth limit reached: ", obj);
				}
			}
			
			if((obj === null || typeof obj !== "object") || (callDepth === depth || callDepth === this.CLONE_depthLimit)){
				return obj;
			}
			if(obj instanceof Date){
				return new Date(obj.getTime());
			}
			if(Array.isArray(obj)){
				var retArr=[];
				for(var i=0; i<obj.length; i++)
				{
					
					retArr[i]=this.CLONE(obj[i], depth, callDepth+1);
				}
				return retArr;
			}
			
			if(obj.CLONE){
				return obj.CLONE();
			}
			var ret={};
			for(var mem in obj)
			{
				if(obj.hasOwnProperty(mem)){
					ret[mem]=this.CLONE(obj[mem], depth, callDepth+1);
				}
			}
			return ret;
		};
	})()
};

/* Array.isArray polyfill */
if(!Array.isArray){
	Array.isArray=function(a){
		if(typeof a === "object" && a.constructor === Array){
			return true;
		}
	}
}
	

/* END _object*/

</script>
<script id='js_library/fun2'>
/* this is just so we have a function when we have to execute something,
// so we don't have to create an empty function every time (expensive in JS) */
function DUMMY_FUNCT(){}
_fun={
	curryScope:function(fun,scp){
		return function(){
			fun.apply(scp,arguments);
		};
	},
	curryArgs:function(fun, arg){
		if(!Array.isArray(arg)){throw new TypeError('Arg must be an array');}
		return function(){
			fun.apply({}, arg);
		};
	},
	curryScopeArgs:function(fun,scp,arg){
		if(!Array.isArray(arg)){throw new TypeError('Arg must be an array');}
		return function(){
			fun.apply(scp,arg);
		};
	},
	/* run an array of functions. keep indicates whether to erase the array after */
	RunQue:function(arr, keep){
	   arr.forEach(function(a){a();});
	   if(!keep){arr.length=0;}
	}
};
</script>
<script id='js_core/start'>
// CORE
var el_body, el_html;
var sceenWidth, screenHeight;
function IGNITE(){
	el_body=document.getElementsByTagName("body")[0];
	el_html=document.getElementsByTagName("html")[0];
	
		screenWidth=el_html.clientWidth;
        screenHeight=el_html.clientHeight;
        window.onresize=function(){
			screenHeight=el_html.clientHeight;
			screenWidth=el_html.clientWidth;
        }
		_hist.GRAB_addr();
		VCR.main.CHANGE();
}
</script>
<script id='js_library/el'>
/* START _element */
_el={
	/* cancel an event */
    CancelEvent:function(e){e.preventDefault(); e.cancelBubble=true;},
	/* move an id from 1 element to another */
    MoveId:function(id,el){
       (document.getElementById(id) || {}).id='';
       el.id=id;
    },
	/* this is a helper for CREATE */
    PARSE_element:function(a){
       if(typeof a === "string"){return this.TEXT(a);}
       return a;
    },
	/* remove an element */
	REMOVE:function(e){
		if(e && e.parentNode){e.parentNode.removeChild(e);}
	},
	/* append an element to a parent, returns the parent */
	APPEND:function(p,c){
		if(Array.isArray(c)){
			for(var i=0; i<c.length; i++)
			{
				p.appendChild(this.PARSE_element(c[i]));
			}
		}else{
			p.appendChild(this.PARSE_element(c));
		}
		return p;
	},
	/* append an element to a parent, returns the child */
	_APPEND:function(p,c){
		if(Array.isArray(c)){
		   c.forEach(function(a){p.appendChild(_el.PARSE_element(c));});
		}else{p.appendChild(this.PARSE_element(c));}
			

		return c;
	},
	/* 
		create an element: 
			tp is the tag name 
			id is id
			className is className 
			otherMemOb 
				is anything to insert as a property on the element
				two special properties:
					'style'
						and
					'attributes'
			append 
				an array of elements to append to the created element
				raw strings are created as text nodes
	*/
	CREATE:function(tp, id, className, otherMemOb, append){
		var ret=document.createElement(tp);
		if(id){
			ret.id=id;
		}
		if(className){
			ret.className=className;
		}
		if(otherMemOb){
			for(var mem in otherMemOb)
			{
				if(mem === "style"){
					for(var s in otherMemOb[mem])
					{
						ret.style[s]=otherMemOb[mem][s];
					}
				}else if(mem === 'attributes'){
					for(var a in otherMemOb[mem]){ret.setAttribute(a, otherMemOb[mem][a]);}
				}else{
					ret[mem]=otherMemOb[mem];
				}
			}
		}
		if(append){
		   this.APPEND(ret, append);
		}
		return ret;
	},
	/* create a text node */
	TEXT:function(txt){
		return document.createTextNode(txt);
	},
	/* removes all child elements */
	EMPTY:function(el){
		if(el && el.childNodes){
			for(var i=0; i<el.childNodes.length; i++)
			{
				if(el.childNodes[i]){
					el.removeChild(el.childNodes[i]);
					i--;
				}
			}
		}
	}
};


/* END _element */
</script>
<script id='js_library/VCR2'>
/*
	@param targetFunct : function : opt : a function to return the element that the controler has control over
	@param insertOb    : object   : opt : an object to insert any object values into the controler that you want
	@param config      : object   : opt : an object for the configuration 
			config structure:{
				noLog       : bool   : set to true to not register any changes in the history,
				noLogByView : object : disable logging for individual views by their numeric index
			}
			
	This is the main constructor of the library.
	
	I hope the arguments are all self explanitory. They are all optional.
	
	One important note about useage. To instantiate a controler, please use this form:
	
        VVV Use whatever name you want
	VCR.main = new VC();
	
	The history library assumes you instantiated this in this fashion, and iterates though the VCR variable when saving application state.
*/

function VC(targetFunct, insertOb, config){
	/* view indexes */
	this.currentView=0;
	this.previousView=0;
	this.nextView=0;
	
	/* a list of functions */
	this.views=[];
	
	/* 
		these maps map the names and indexes between each other. 
		Safe map can convert both an index and a name back to an index.
	*/
    this.indexMap={}; 
    this.safeMap={};

	/* overwrites the target funct if one has been provided. */
	if(targetFunct){
		this.targetFunct=targetFunct;
	}

	
	this.active=false;
	this.config=config || {};
	this.activeConfig=this.config;
	
	
	/* event ques */
	this.onchange=[];
	this.afterchange=[];
	this.onrelease=[];
	this.onlaunch=[];
	
	this.historyChange=null;
	
	
	this.captured=false;
	this.capParent=null;
	this.capTargetFunct=null;
	this.capConfig=null;
	this.capChildren=[];

	/* these registries can be used to register things that need cleaning up upon change */
	this.reg_elements=[];
	this.reg_timeouts=[];
	this.reg_intervals=[];
	this.reg_goodObjects=[];
	
	if(insertOb){
		for(var mem in insertOb)
		{
		   if(typeof this[mem] !== "undefined"){
			  throw new TypeError("The member "+mem+" already exists in the view controller");
		   }
		   this[mem]=insertOb[mem];
		}
	}
}
VC.prototype.is_VC=true; /* a quick check so you don't have to check against prototype */

/* This function is the default target function. This is called to indicate the container that the view controller has control of. */
VC.prototype.targetFunct=function(){
	return document.body;
}

/* returns a lits of the names of all the views */
VC.prototype.GET_viewList=function(){
   return Object.values(this.indexMap);
}

/*
@param emptyTarget : bool : opt : set to true to empty the target element before returning

This function gets the element that the view controler is in charge of.
*/
VC.prototype.GET_target=function(emptyTarget){
	var ret;
	if(this.captured && this.capTargetFunct){
		ret=this.capTargetFunct();
	}else{
		ret=this.targetFunct();
	}
	if(emptyTarget){
		_el.EMPTY(ret);
	}
	return ret;
}

/*

@param v : (int | string) : opt : the name or index of a view

This function gets the name of the a view by either the index or the name. 

If v is not provided, the current view of the view controler is returned.
*/
VC.prototype.GET_viewName=function(v){
    return this.indexMap[this.safeMap[v || this.currentView]];
}

/*
@param dat : object : req : an object to be the view data associated with the view.

This function is here to not only set the view data of a view,
but to also register a listener to reset the view data of a view onchange,
so that the data doesn't hang around when re-visiting a view
*/
VC.prototype.REGISTER_viewData=function(dat){
   var nm=this.GET_viewName();
   this[nm].viewData=dat;
   var sv=this[nm];
   this.REGISTER_changeANDrelease(function(){sv.viewData={};});
   
}

/*
@param dat : object : req : an object to be the view data associated with the view.

This function is here to pre-set the view data of an upcoming view, 
so that the history imprint stays in sync. This is called from the history library.
*/
VC.prototype.PUSH_viewData=function(dat){
   this.stagedViewData=dat;
}

/* returns the view data of the current view */
VC.prototype.GET_viewData=function(){
   return this[this.GET_viewName()].viewData;
}

/*
	@param name     : string   : req : a string to name the view.
	@param f        : function : req : a function with 1 argument (the parent view controller) to modify the container
	@param insertOb : object   : opt : this object is for the data-holding of the view. It can be anything you want, but viewData is reserved for use the view data to be saved with the view history
	@param config   : object   : opt : this object is for the view configuration. Currently unused
	
	This function registers the view with the view controler. 
	
	It pushes the callback function onto the views array, and registers the name in all of the necessary mapping objects.
	
	The insertOb is where you can indicated default viewData. 
	
	Right now, the config is unused.
	
*/
VC.prototype.REGISTER_view=function(name, f, insertOb, config){
        if(typeof this[name] !== "undefined"){throw new TypeError('Name already exists on this view controller: '+name);}
	this.views.push(f);
        if(insertOb && !insertOb.viewData){insertOb.viewData={};}
	this[name]=insertOb || {viewData:{}};
         var i=this.views.length-1;
         this.safeMap[name]=i; this.safeMap[i]=i;
         this.indexMap[i]=name;
	this.config[name]=config || {};
}

/* registers a function to be called upon change or realease */
VC.prototype.REGISTER_changeANDrelease=function(f){
        this.onrelease.push(f);this.onchange.push(f);
}

/* registers a function to be called upon release */
VC.prototype.REGISTER_release=function(f){
   this.onrelease.push(f);
}

/* registeres an object to have for  o.good=false; upon change */
VC.prototype.REGISTER_goodObject=function(o){
	this.reg_goodObjects.push(o);
	return o;
}

/* registers an element to be removed upon change */
VC.prototype.REGISTER_element=function(e){
    this.reg_elements.push(e);
    return e;
}

/* registers a timeout to be cleared upon change */
VC.prototype.REGISTER_timeout=function(t){
    this.reg_timeouts.push(t);
    return t;
}

/*  registers an interval to be canceled upon change. */
VC.prototype.REGISTER_interval=function(i){
    this.reg_intervals.push(i);
    return i;
}

/* iterates through all of the registries and cleans up with the apropriate action */
VC.prototype.CLEANUP=function(){
    while(this.reg_elements.length){
           _el.REMOVE(this.reg_elements.pop());
        }
        while(this.reg_timeouts.length){
           clearTimeout(this.reg_timeouts.pop());
        }
        while(this.reg_intervals.length){
           clearInterval(this.reg_intervals.pop());
        }
		while(this.reg_goodObjects.length){
			var r=this.reg_goodObjects.pop().good=false;
			if(r.target){_el.REMOVE(r.target);}
		}
        while(this.capChildren.length)
        {this.capChildren.pop().RELEASE();}
}

/*
	@param v      : int|string : opt : the view you want to change to
	@param dat    : object     : opt : an object to register 
	@param f      : function   : opt : a function to be called after the change has taken place
	@param config : object     : opt : a configuration object to overwrite the config of the VC 
		config sturcture{
			noLog : bool : set to true to not log in the history
		}
		
	This is the meat an potatoes.
	
	Call this function to change the view 
	
	v can be an index or the name of a view... 
	
	OR v can be omitted, and it'll change to this.stagedView || this.currentView
*/
VC.prototype.CHANGE=function(v, dat, f, config){
	VC.prototype.VCR_depth++;
	switch(typeof v){
		case "string":
		case "number":
		   break;
		 default:
			v=this.stagedView || this.currentView;
			this.stagedView='';
			break;
	}
	this.CLEANUP();
	var arr, arr2;
	if(this.views.length){
		v=this.safeMap[v];
		this.previousView=this.currentView;
		this.currentView=v;
	


		this.active=true;
		var lgger;
		if(
			!this.config.noLog 
			&& (!config || config && !config.noLog) 
			&& (!this.config.noLogByView || !this.config.noLogByView[v])
			&& this.VCR_depth === 1
		){
			lgger=this.LOG_change;
		}
		VC.prototype.VCR_depth--;

		if(this.stagedViewData){
			dat=this.stagedViewData; this.stagedViewData=null;
		}

		_fun.RunQue([
			_fun.curryArgs(_fun.RunQue, [this.onchange]),
			_fun.curryScope(function(){if(dat){this.REGISTER_viewData(dat);}}, this),
			_fun.curryArgs(this.views[v],[this]),
			_fun.curryArgs(_fun.RunQue, [this.afterchange]),
			f || DUMMY_FUNCT, 
			_fun.curryScope(lgger || DUMMY_FUNCT,this)
		]);
		this.historyChange=null;
	}
}

/* this is used to determine when it's time to write to the browser's history */
VC.prototype.VCR_depth=0; 

/*
	@param v      : string|int : opt : v is the view to launch into
	@param dat    : object     : opt : dat is the viewData object to pass to CHANGE
	@param f      : function   : opt : f is a function to be called after the change
	@param config : object     : opt : config is a config to overwrite the config of the VC
	
	This function launches a view from the view function of another view controler.
	
	It needs to be differentiated from change because of reasons that have to do with the history registry.
*/
VC.prototype.LAUNCH=function(v, dat, f, config){
	_fun.RunQue([
		_fun.curryArgs(_fun.RunQue, [this.onlaunch, true]),
		_fun.curryScopeArgs(this.CHANGE, this, [v,dat,f,config])
	]);
}

/*
	@param par  : VC       : req : the VC that is capturing the VC
	@param tar  : function : req : a function to return the new target
	@param conf : object   : opt : an object to overwrite the default configuration of the vc
	
	This function captures a VC, changing its behaviour to yield to the parent VC.
*/
VC.prototype.CAPTURE=function(par, tar, conf){
	conf=conf || {};
	if(par && par.is_VC){
		par.capChildren.push(this);
		this.captured=true;
		this.capParent=par;
		this.capTargetFunct=tar;
		this.capConfig=_ob.COMBINE(this.config, conf);
		this.activeConfig=this.capConfig;
	}else{
		throw new TypeError("par was not a view controler...", par);
	}
}

/* this function releases a view controler, and returns it to its default behavior */
VC.prototype.RELEASE=function(){
    this.active=false;
	this.captured=false;
	this.capParent=undefined;
	this.capTargetFunct=undefined;
	this.capConfig=undefined;
	this.activeConfig=this.config;
    this.CLEANUP();        
	_fun.RunQue(this.onrelease);
	_fun.RunQue(this.onchange);
	if(this.config.resetViewOnRelease){
		if(this.historyChange === true){
			this.historyChange=null;
		}else{
			this.currentView=0;
		}
	}
}

/* go to next view */
VC.prototype.INCR=function(){
    this.CHANGE((this.currentView+1)%this.views.length);
}

/* go to previous view */
VC.prototype.DECR=function(){
    var c=this.currentView-1; 
    if(c<0){c=this.views.length-1;}
    this.CHANGE(c);
}

/* asks if a particular view is present in the view controler */
VC.prototype.HAS_view=function(str){
    return (Object.values(this.safeMap).indexOf(str) >= 0);
}

/* the global variable to hold all your VC instantiations.
	the history library looks here to save view state.*/
var VCR={};
</script>
<script id='js_library/hist'>
_hist={
	incrId:0,
   firstHistory:true,
   uriOb:{},
	url:false,
   logflag:true,
   documentTitle:'',
   globOb:null,
   GRAB_addr:function(){
      if(history && history.state && history.state.VCR){
         var VCRaddr=history.state.VCR;
         for(var mem in VCRaddr)
         {
             VCR[mem].historyChange=true;
             VCR[mem].stagedView=''+VCRaddr[mem].view;
             if(VCRaddr[mem].viewData){
                VCR[mem].PUSH_viewData(VCRaddr[mem].viewData);
             }
         }
      }
	   
   }
};


if(history && history.state && history.state.stateId){
	_hist.incrId=history.state.stateId;
}

if(history && history.pushState){
   VC.prototype.LOG_change=function(){
          var uriOb={};
          
                   
		  for(var mem in VCR)
		  {
			 if(VCR[mem].active && !VCR[mem].config.noLog){
                                var view;
				uriOb[mem]={
                                   view:(view=VCR[mem].currentView)
                                };
                                var vd=VCR[mem][VCR[mem].GET_viewName()].viewData;
                                if(!Object.keys(vd).length){vd=false;}
                                if(vd){
                                   uriOb[mem].viewData=VCR[mem][VCR[mem].GET_viewName()].viewData;
                                }
                                if(VCR[mem].captured){
                                   uriOb[mem].captured=true;
                                }
			 }
		  }
		  var globOb=_hist.globOb || {};
                  if(_hist.logflag){
		     if(!_hist.firstHistory && !_ob.COMPARE(uriOb, _hist.uriOb)){
  		       _hist.incrId++;
			   history.pushState(_hist.lastState=_ob.COMBINE({VCR:uriOb, stateId:_hist.incrId}, globOb),"",_hist.url || undefined);  
		     }else{
   	 		_hist.firstHistory=false;
  
			    history.replaceState(_hist.lastState=_ob.COMBINE({VCR:uriOb, stateId:_hist.incrId},globOb),"", _hist.url || undefined);  
		     }
                     
                  }
                   
			_hist.url=false;
                  _hist.uriOb=uriOb;
                  if(_hist.documentTitle){document.title=_hist.documentTitle; _hist.documentTitle="";}
	  
   };
   onpopstate=function(){
           var state=history.state;
           if(!state){
              history.replaceState(_hist.lastState,'',location.href);
              return;
           }else{
              state=state.VCR;
           }
           _hist.GRAB_addr();
           _hist.logflag=false;
           for(var mem in state)
           {
              if(!state[mem].captured){
                 VCR[mem].CHANGE();
              }
           }
	   _hist.logflag=true;
   };
}else{
   console.warn('no history supported');
}
</script>
<script id='js_library/dFCM'>
function dFCM(fileName, config){
	this.fileName=fileName;
	this.VC=null;
	this.target=null;
	
	this.config=config || {};
	this.configOverride={};
}
dFCM.prototype.CAPTURE=function(VC, target, config){
	this.VC=VC || null; this.target=target || null; this.configOverride=config || {};
}
dFCM.prototype.RELEASE=function(){
	this.VC=null;
	this.target=null; 
	this.configOverride={};
}
dFCM.prototype.INSERT_value=function(rec, name, config, def){
    var r;
	if(typeof config[name] !== 'undefined'){
		r=config[name];
	}else if(typeof this.configOverride[name] !== "undefined"){
		r=this.configOverride[name];
	}else if(typeof this.config[name] !== "undefined"){
		r=this.config[name];
	}else if(typeof def !== 'undefined'){
		r=def;
	}
	if(typeof r === 'object'){
		r=_ob.CLONE(r);
	}
	
	return rec[name]=r;
	
}
dFCM.prototype.PARSE_value=function(name, config, def){
	if(typeof config[name] !== 'undefined'){
		return config[name];
	}
	if(typeof this.configOverride[name] !== "undefined"){
		return this.configOverride[name];
	}
	if(typeof this.config[name] !== "undefined"){
		return this.config[name];
	}
	if(typeof def !== 'undefined'){
		return def;
	}
	return undefined;
}
dFCM.prototype.RETRY=function(){
	this.CALL_data(this.callFun, this.callDat, this.callConfig);
};
dFCM.prototype.CALL_data=function(fun, dat, config){
	this.callFun=fun; this.callDat=dat; this.callConfig=config;
	config=config || {};
	var tar;
	_el.APPEND(this.target || _el.CREATE('div'), tar=_el.CREATE('div'));
	
	this.waitingFunction(tar);
	var stillGoodObject={good:true, target:tar};
	if(this.VC){
		this.VC.REGISTER_goodObject(stillGoodObject);
	}
	
	var reqOb={};
	this.INSERT_value(reqOb, 'method',config);
	this.INSERT_value(reqOb,'headers',config);
	this.INSERT_value(reqOb,'mode',config);
	this.INSERT_value(reqOb,'cache',config);
	this.INSERT_value(reqOb,'credentials',config);
	this.INSERT_value(reqOb,'redirect',config);
	this.INSERT_value(reqOb,'referrerPolicy',config);
	this.INSERT_value(reqOb, 'body', config, dat);
	
	//console.log('heythere', _ob.CLONE(reqOb), _ob.CLONE(config), _ob.CLONE(this.config));
	if(reqOb.headers && reqOb.headers['Content-Type'] === 'special/json'){
		reqOb.headers.contentType='application/json';
		reqOb.body=JSON.stringify(reqOb.body);
	}else if(reqOb.headers && reqOb.headers['Content-Type'] === 'special/obPost'){
		//console.log('yo you duede');
		reqOb.headers['Content-Type']="application/x-www-form-urlencoded";
		var uriStr="";
		for(var mem in reqOb.body)
		{
			if(typeof reqOb.body[mem] === "object"){
				reqOb.body[mem]=JSON.stringify(reqOb.body[mem]);
			}
			if(uriStr){uriStr+="&";}
			uriStr+=encodeURIComponent(mem)+"="+encodeURIComponent(reqOb.body[mem]);
		}
		reqOb.body=uriStr;
	}else if(config.useFormData || this.config.useFormData){
		var fd=new FormData();
		for(var mem in reqOb.body)
		{
			if(typeof reqOb.body[mem] === "object" && !(reqOb.body[mem] instanceof Blob)){
				reqOb.body[mem]=JSON.stringify(reqOb.body[mem]);
			}
			fd.append(mem, reqOb.body[mem]);
		}
		reqOb.body=fd;
	}
	if(reqOb.method === "GET" || reqOb.method === "HEAD" || !reqOb.method){
		delete reqOb.body;
	}
	
	var t=this;
	
		fetch(this.fileName, reqOb).then(t.checkResponseCode(config))
		.then(function(resDat){
			t.doneFunction();
			if(resDat.statusFail){
				console.log('statusFail', resDat);
				_el.REMOVE(tar);
				return;
			}
			if(stillGoodObject.good){
				fun(resDat);
			}
			_el.REMOVE(tar);
		}).catch(function(e){ t.handleError(e);});
	

	
}
dFCM.prototype.waitingFunction=function(target){
	
}
dFCM.prototype.doneFunction=function(){
	
}
dFCM.prototype.checkResponseCode=function(config){
	var t=this;
	return function(result){
		var rr=t.customResponseCodeCheck(result);
		if(rr){
			return new Promise(function(ro){ro({statusFail:true, message:rr});});
		}
		return result[t.PARSE_value('responseType',config, 'text')]();
	}
}
dFCM.prototype.customResponseCodeCheck=function(result){
	return '';
}
dFCM.prototype.handleError=function(e){
	return console.log('there was an error ',e);
}
</script>
<script id='js_library/rmf'>
RMF_basicNameArr=[
   "nameStack","formCol"
];
function EXTRACT_basic(a,b){
   for(var mem in RMF_basicNameArr)
   {
      a[mem]=b[mem]
   }
}

function RMFconcatName(nameStack, name){
  
   if(name || name === 0){
      if(nameStack){
         return nameStack+"-"+name;
      }return name;
   }return "";
}
function RMFgetName(config){
   return RMFconcatName(config.nameStack, config.name);
}

function RMFidHelper(id, app){
   app= app || "";
   if(id){
      return id+app;
   }return "";
}
function RMFclassHelper(cls, runner, cust, app){
   app=app || "";
   cls=cls || "";
   runner= runner || "";
   var ret="";
   
   ret+=cls+" ";
   if(runner){
      ret+=runner+app+" ";
   }
   if(cust){
      ret+=cust+app;
   }
   return ret.trim();
}
function RMFid(config, id){
   return RMFidHelper(config.id, id);
}
function RMFclass(config, cls, app){
   return RMFclassHelper(cls, config.classRunner, config.class, app);
}
function RMFsequenceHelper(text,client,tf){
    if(tf){
       return [client,text];
    }return [text,client];
}
function RMFdefaults(conf, def, specials){
   if(typeof conf !== "object"){
      throw new TypeError("Conf has to be an object");
   }
   def=def || {}; specials=specials || {};
   for(var mem in def)
   {
       if(typeof conf[mem] === "undefined"){
          conf[mem]=def[mem];
       }
   }
   for(var mem in specials)
   {
      if(specials[mem] === "rawBool"){
         conf[mem] = !!conf[mem]; 
      }
   }
}

function RMF_extractForExtension(ret, ob){
   for(var mem in ob)
   {
       ret[mem]=ob[mem];
   }
    if(ob.COLL){ob.EXT_OLDCOLL=ob.COLL;}
}
/*
  all configs get::: 
    nameStack - running name stack for nested inputs
    forColl   - the object to be recieving the form collection object
  from the MAKE function
*/
function RMFbasicRawInput(rmfdefault, rmfspecials, coll, set, bodyFunction){
   
    rmfdefault=rmfdefault || {};
    rmfspecials=rmfspecials || {};
    coll = coll;
    set = set || function(v){this.inp.value=v;}
    bodyFunction= bodyFunction || function(config,ret){
      ret.inp=ret.el=_el.TEXT("No body function provided.");
    }
    
    RMFdefaults(rmfdefault, {
        name:"",
        id:"",
        class:"",
        default:""
    });

    return function(config){
       config=config || {};
      // console.log(rmfdefault);
       RMFdefaults(config,rmfdefault, rmfspecials);
       //console.log(config);
       var ret={
           COLL:coll,
           SET:set
       };
       bodyFunction.call(this,config, ret);
       
       ret.SET("");
       return ret;
    } 
}
function RMFbasicInput(rmfdefault, rmfspecials, coll, set, bodyFunction, labelType){
    rmfdefault=rmfdefault || {};
    rmfspecials=rmfspecials || {};
    coll = coll;
    set = set || function(v){this.inp.value=v;}
    bodyFunction= bodyFunction || function(config,ret){
      ret.inp=ret.el=_el.TEXT("No body function provided.");
    }
    
    RMFdefaults(rmfdefault, {
        name:"",
        id:"",
        class:"",
        default:"",
        classRunner:""
    });
    return function(config){
       config=config || {};
      // console.log(rmfdefault);
       RMFdefaults(config,rmfdefault, rmfspecials);
       //console.log(config);
       var ret={
           COLL:coll,
           SET:set
       };
       bodyFunction.call(this,config, ret);
       this.basicLabelError({
          inp:ret.inp,
          otherRet:ret,
          labelText:config.labelText,
          labelSequence:config.labelSequence,
          labelType:labelType,
          errorText:config.errorText,
          errorSequence:config.errorSequence,
          data:config.data,
          dataType:config.dataType,
          classRunner:config.classRunner,
          class:config.class
       });
       ret.SET(config.default);
       return ret;
    }
}

/*
  all configs get::: 
    nameStack - running name stack for nested inputs
    forColl   - the object to be recieving the form collection object
  from the MAKE function
*/

function RMFbasicMultiInput(rmfdefault, rmfspecials, coll, inpList, insOb){
    rmfdefault=rmfdefault || {};
    rmfspecials=rmfspecials || {};
    coll = coll || function(a){return a;}
    
    insOb=insOb || {};
    
    RMFdefaults(rmfdefault, {
        name:"",
        id:"",
        class:"",
        default:"",
        preInps:{}
    });

    //alert("hey");
    return RMFbasicInput(rmfdefault, rmfspecials, function(){
       return coll(this.newCol.COLL());
    },
    function(v){
     //  console.log(this.newCol);
       this.newCol.SET(v);
    },//SET
    function(config, ret){
        ret.inp=_el.CREATE("div","","",{});
        
        for(var mem in insOb)
        {
          ret.inp[mem]=(function(m){
             return function(){
                 insOb[m].CALL(ret);
             }
          })(mem)
        }

        for(var i=0; i< inpList.lengh; i++)
        {
           inpsList[i]=_ob.COMBINE(inpList[i], config.preInps[inpList[i].name] || {});
        }

        var newColOb={};
        ret.newCol=newCol=new FORM_COL_OB(newColOb);
        MAKE(ret.inp, inpList, newColOb, RMFconcatName(config.nameStack, config.name), config.inpsConfig);
    },"section");
}

/*
  all configs get::: 
    nameStack - running name stack for nested inputs
    formColl   - the object to be recieving the form collection object
  from the MAKE function
*/



var INPTYPES={
   CURRY:function(name){
      var t=this;
      return function(config){
         return t[name](config);
      }
   },
   "span":function(config){
      RMFdefaults(config, {
        name:"", id:"", class:"", default:"", classRunner:""
      });
      var ret= {
        el:_el.CREATE('span', RMFid(config, "span"), RMFclass(config, "RMFspans"))
      };
      if(config.text){
         _el.APPEND(ret.el, _el.TEXT(config.text));
      }else if(config.element){
         _el.APPEND(ret.el, config.element);
      }
      return ret;
   },
   "basicLabelError":function(config){
       /*config is an object
          inp:the input to put in label and error,
          errorText: text for the error,
          errorSequence: true for the error after the input false for before,
          labelText: text for the label,
          labelSequence: true for the label after the input false for before,
          labelType: ENUM("label","section"),
          otherRet: a ret to recieve the members
          
       */
       config=config || {};
       //console.log(config.otherRet);
       RMFdefaults(config, {
         inp:_el.TEXT("ERROR: There was no inp provided"),
         errorText:"",
         labelText:"",
         errorSequence:true,
         labelSequence:false,
         labelType:"label",
         otherRet:{}
       },{
         labelSequence:"rawBool",
         errorSequence:"rawBool" 
       });
       
       var ret={};
       var errorBox=this.error({
         erroree:config.inp,
         text:config.errorText,
         sequence:config.errorSequence,
         classRunner:config.classRunner,
         class:config.class
      });
      ret.errorBox=errorBox.el;
      ret.errorTextBox=errorBox.textBox;
      
      var labelBox;
      if(config.labelType === "label"){
         labelBox=this.label({
            labelee:ret.errorBox,
            text:config.labelText,
            sequence:config.labelSequence,
            data:config.data,
            dataType:config.dataType,
            classRunner:config.classRunner,
            class:config.class
         });
      }else{
         labelBox=this.sectionLabel({
            labelee:ret.errorBox,
            text:config.labelText,
            sequence:config.labelSequence,
            data:config.data,
            dataType:config.dataType,
            classRunner:config.classRunner,
            class:config.class
         });
      }
      ret.el=ret.label=labelBox.el;
      ret.labelTextBox=labelBox.textBox;
       
       for(var mem in ret)
       {config.otherRet[mem]=ret[mem];}
       return ret;
   },
   "label":function(config){
      /*config is an object
         text: text for the label,
         labelee: element to be labeled,
         sequence: false for text before labelee true for after,
         id: an id to pass around,
         class: a class to pass around,
         name: a name to pass around
      */
      config=config || {};
      RMFdefaults(config, {
        text:"",
        labelee:_el.TEXT("No labelee given"),
        id:"",class:"",name:"",classRunner:""
      },{
        sequence:"rawBool"
      });

      var ret={};
      
      ret.el=_el.CREATE('label',RMFid(config, "label"),RMFclass(config, "RMFlabels","label"),{},RMFsequenceHelper(
         ret.textBox=_el.CREATE('span',RMFid(config, "textBox"),RMFclass(config, "RMFtextBoxes","textBox"),{},[RMFgetCard(config, 1)]),
         config.labelee,
         config.sequence
      ));

      


      return ret;
   },
   "sectionLabel":function(config){
       /*config is an object
          text: text for the label,
         labelee: element to be labeled,
         sequence: false for text before labelee true for after,
         id: an id to pass around,
         class: a class to pass around,
         name: a name to pass around
       */
      config=config || {};
      RMFdefaults(config, {
        text:"",
        labelee:_el.TEXT("No labelee given"),
        id:"",class:"",name:"",classRunner:""
      },{
        sequence:"rawBool"
      });

      var ret={};
      
      ret.el=_el.CREATE('div',RMFid(config, "sectionLabel"),RMFclass(config,"RMFsectionLabels", "sectionLabel"),{},RMFsequenceHelper(
         ret.textBox=_el.CREATE('div',RMFid(config, "textBox"),RMFclass(config,"RMFtextBoxes", "textBox"),{},[RMFgetCard(config, 0)]),
         config.labelee,
         config.sequence
      ));

      return ret;
   },
   "error":function(config){
      /*config is an object
         text: text for the label,
         erroree: element to be labeled for errors,
         sequence: false for text before labelee true for after,
         id: an id to pass around,
         class: a class to pass around,
         name: a name to pass around
      */
      config=config || {};
      RMFdefaults(config,{
          text:"",
          erroree:_el.TEXT("No erroree given"),
          id:"",class:"",name:"", classRunner:""
      },{
          sequence:"rawBool"
      });

      var ret={};
      
      ret.el=_el.CREATE('span',RMFid(config, "errorBox"),RMFclass(config, "RMFerrorBoxes","errorBox"),{},RMFsequenceHelper(
         ret.textBox=_el.CREATE('div',RMFid(config, "errorBoxTextBox"),RMFclass(config, "RMFtextBoxes","textBox"),{},[_el.TEXT(config.text)]),
         config.erroree,
         config.sequence
      ));

      return ret;
   },
   "header":function(config){
      /*config==>
         {
             level: the level of the header,
             text: the text in the header,
             id: an id for the header,
             class: a class to give to the header,
             name:a name for the id
         }
      */     
      config=config || {};
      config.nameStack=config.nameStack || "";
      config.name=config.name || "";
      config.wholeName=RMFconcatName(config.nameStack, config.name);
      config.level=config.level || 1;
      config.text=config.text || "";

      config.id=config.id || "";

      config.class= config.class || "";


      var ret={};
      ret.el=_el.CREATE('h'+config.level, RMFid(config, ""), RMFclass(config, "RMFheader","header"), {},[_el.TEXT(config.text)]);
      return ret;
   },
   "section":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFsection",
        inps:[]// inps: a list of inputs
      },/// rmfdefault
      {},// rmfspecials
      false,// COLL
      false,//SET
      function(config,ret){
        // console.log(config);
         ret.inp=_el.CREATE("div", RMFid(config), RMFclass(config, "RMFsection"), {}); 
         MAKE(ret.inp, config.inps, config.formCol, config.nameStack, config.inpsConfig);
      },
      "section"//labelType
   ),
   "compound":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFcompound",
        inps:[]// inps: a list of inputs
      },/// rmfdefault
      {},// rmfspecials
      function(){
         return this.FCOLL.COLL();
      },// COLL
      function(a){
         this.FCOLL.SET(a);
      },//SET
      function(config,ret){
        // console.log(config);
         ret.inp=_el.CREATE("div", RMFid(config), RMFclass(config, "RMFcompound"), {}); 
         var fc={};
         ret.FCOLL=new FORM_COL_OB(fc);
        // console.log('from compound', config);
         MAKE(ret.inp, config.inps, fc, RMFconcatName(config.nameStack, config.name), config.inpsConfig);
      },
      "section"//labelType
   ),
   "dynamicList":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFdynamicList",
        inpType:"singleLine", // listType is a string that is the name of the member of INPTYPES
        addText:"+Add", // the text to be in the add input button
        removeText:"X", // the text to be in the remove input button
        inpConfig:{}, // inpConfig: object to be passed to the listType function
        inpText:"",// inpText: text for each of the child input labels 
        beforeOrAfter:false
        // removeListener
        // inpTypeFunct // a function that returns the equivilant of what an inp type would
      },/// rmfdefault
      {},// rmfspecials
      function(){
         var ret=[];
         for(var mem in this.inps)
         {
            ret.push(this.inps[mem].COLL());
         }
         return ret;
      },// COLL
      function(v){
         v=v || [];
         var temp;
  		 this.CLEAR_inps();
         for(var i=0; i<v.length; i++)
         {
            temp=this.ADD_inp();
            temp.SET(v[i]);
         }
      },//SET
      (function(){ 
        function ADD_inp(v){
            var tempInp;// todo 
            if(this.config.inpTypeFunct){
               tempInp=this.config.inpTypeFunct({
                  name:RMFgetName({nameStack:this.config.nameStack, name:RMFconcatName(this.config.name, this.inpInd)}),
                  labelText:this.config.inpText
               });
            }else{
               tempInp=INPTYPES[this.config.inpType](_ob.COMBINE(this.config.inpConfig,{
                  name:RMFgetName({nameStack:this.config.nameStack, name:RMFconcatName(this.config.name, this.inpInd)}),
                  labelText:this.config.inpText
               }));
            }
            var remButton;
            var el=_el.CREATE('div',"",RMFclass(this.config, "RMFdynamicListMems", "dynamicListMem"),{
				attributes:{DATA_inpInd:this.inpInd}
			},[
              remButton=_el.CREATE('input',"",RMFclass(this.config,"RMFdynamicListMemRemoveButtons","dynamicListMemRemoveButton"),{
                 type:"button",
                 value:this.config.removeText,
                 onclick:REMOVE_inp,
                 DATA_ret:this,
                 DATA_inpInd:this.inpInd
               },[]),
               (tempInp).el
           ])
           tempInp.removeButton=remButton;
           this.inps[this.inpInd]=tempInp;
           this.inpInd++;
           if(this.config.beforeOrAfter){
              this.listCatcher.insertBefore(el, this.listCatcher.children[0]);
           }else{
              _el.APPEND(this.listCatcher, el);
           }
           if(this.config.addListener){
              this.config.addListener(tempInp);
           }
           if(v){
			   tempInp.SET(v);
		   }
           return tempInp;
        }
		
		function CLEAR_inps(){
			for(var mem in this.inps)
			{
				this.inps[mem].removeButton.dispatchEvent(new Event('click'));
			}
		}
        function REMOVE_inp(){
           _el.REMOVE(this.parentNode);
           if(this.DATA_ret.config.removeListener){
              this.DATA_ret.config.removeListener(this.DATA_ret.inps[this.DATA_inpInd]);
           }
           delete this.DATA_ret.inps[this.DATA_inpInd];
        }
        var inpFunct;
        return function(config,ret){
           // console.log(config);
           ret.inpFunct=this[config.inpType];
           ret.inpInd=0;
           ret.inps={};
           ret.config=config;
           ret.ADD_inp=ADD_inp;
		   ret.CLEAR_inps=CLEAR_inps;
           ret.inpType=config.inpType;
           ret.inp=_el.CREATE("div", RMFid(config), RMFclass(config, "RMFdynamicList","dynamicList"), {},[]); 
           ret.EMPTY=function(){
              for(var mem in this.inps)
              {
                 this.inps[mem].removeButton.onclick.call(this.inps[mem].removeButton);
              }
           }
           if(!config.beforeOrAfter){
              _el.APPEND(ret.inp, ret.listCatcher=_el.CREATE('div',RMFid(config, "listCatcher"), RMFclass(config,"RMFdynamicListCatcher","dynamicListCatcher")));
           }
           _el.APPEND(ret.inp,_el.CREATE('input',RMFid(config,"addButton"),RMFclass(config, "RMFdynamicListAddButton","dynamicListAddButton"),{
                 type:"button",
                 value:config.addText,
                 onclick:function(){
                    ret.ADD_inp();
                 }
              }));
           if(config.beforeOrAfter){
              _el.APPEND(ret.inp, ret.listCatcher=_el.CREATE('div', RMFid(config, "listCatcher"), RMFclass(config,"RMFdynamicListCatcher","dynamicListCatcher")));
           }
         }
      })(),
      "section"//labelType
   ),
   "dynamicListOrdered":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFdynamicList",
        inpType:"singleLine", // listType is a string that is the name of the member of INPTYPES
        addText:"+Add", // the text to be in the add input button
        removeText:"X", // the text to be in the remove input button
        inpConfig:{}, // inpConfig: object to be passed to the listType function
        inpText:"",// inpText: text for each of the child input labels 
        beforeOrAfter:false
        // removeListener
        // inpTypeFunct // a function that returns the equivilant of what an inp type would
      },/// rmfdefault
      {},// rmfspecials
      function(){
         return Array.from(this.listCatcher.children).map(function(c){return c.FUN_coll();});
      },// COLL
      function(v){
         v=v || [];
         var temp;
  		 this.CLEAR_inps();
         for(var i=0; i<v.length; i++)
         {
            temp=this.ADD_inp();
            temp.SET(v[i]);
         }
      },//SET
      (function(){ 
        function ADD_inp(v){
            var tempInp;// todo 
            if(this.config.inpTypeFunct){
               tempInp=this.config.inpTypeFunct({
                  name:RMFgetName({nameStack:this.config.nameStack, name:RMFconcatName(this.config.name, this.inpInd)}),
                  labelText:this.config.inpText
               });
            }else{
               tempInp=INPTYPES[this.config.inpType](_ob.COMBINE(this.config.inpConfig,{
                  name:RMFgetName({nameStack:this.config.nameStack, name:RMFconcatName(this.config.name, this.inpInd)}),
                  labelText:this.config.inpText
               }));
            }
            var remButton;
            var el=_el.CREATE('div',"",RMFclass(this.config, "RMFdynamicListMems", "dynamicListMem"),{
				attributes:{DATA_inpInd:this.inpInd},
				FUN_coll:function(){
					return tempInp.COLL()
				}
			},[
				_el.CREATE('input','','',{type:'button',value:'T',onclick:function(){
					var par=this.parentNode;
					var parpar=par.parentNode;
					var ps=par.previousSibling;
					if(ps){
						_el.REMOVE(par);
						parpar.insertBefore(par, ps);
					}
				}}),
				_el.CREATE('input','','',{type:'button',value:'V', onclick:function(){
					var par=this.parentNode;
					var parpar=par.parentNode;
					var ns=(par.nextSibling || {}).nextSibling;
					_el.REMOVE(par);
					parpar.insertBefore(par, ns);

				}}),
              remButton=_el.CREATE('input',"",RMFclass(this.config,"RMFdynamicListMemRemoveButtons","dynamicListMemRemoveButton"),{
                 type:"button",
                 value:this.config.removeText,
                 onclick:REMOVE_inp,
                 DATA_ret:this,
                 DATA_inpInd:this.inpInd
               },[]),
               (tempInp).el
           ])
           tempInp.removeButton=remButton;
           this.inps[this.inpInd]=tempInp;
           this.inpInd++;
           if(this.config.beforeOrAfter){
              this.listCatcher.insertBefore(el, this.listCatcher.children[0]);
           }else{
              _el.APPEND(this.listCatcher, el);
           }
           if(this.config.addListener){
              this.config.addListener(tempInp);
           }
           if(v){
			   tempInp.SET(v);
		   }
           return tempInp;
        }
		
		function CLEAR_inps(){
			for(var mem in this.inps)
			{
				this.inps[mem].removeButton.dispatchEvent(new Event('click'));
			}
		}
        function REMOVE_inp(){
           _el.REMOVE(this.parentNode);
           if(this.DATA_ret.config.removeListener){
              this.DATA_ret.config.removeListener(this.DATA_ret.inps[this.DATA_inpInd]);
           }
           delete this.DATA_ret.inps[this.DATA_inpInd];
        }
        var inpFunct;
        return function(config,ret){
           // console.log(config);
           ret.inpFunct=this[config.inpType];
           ret.inpInd=0;
           ret.inps={};
           ret.config=config;
           ret.ADD_inp=ADD_inp;
		   ret.CLEAR_inps=CLEAR_inps;
           ret.inpType=config.inpType;
           ret.inp=_el.CREATE("div", RMFid(config), RMFclass(config, "RMFdynamicList","dynamicList"), {},[]); 
           ret.EMPTY=function(){
              for(var mem in this.inps)
              {
                 this.inps[mem].removeButton.onclick.call(this.inps[mem].removeButton);
              }
           }
           if(!config.beforeOrAfter){
              _el.APPEND(ret.inp, ret.listCatcher=_el.CREATE('div',RMFid(config, "listCatcher"), RMFclass(config,"RMFdynamicListCatcher","dynamicListCatcher")));
           }
           _el.APPEND(ret.inp,_el.CREATE('input',RMFid(config,"addButton"),RMFclass(config, "RMFdynamicListAddButton","dynamicListAddButton"),{
                 type:"button",
                 value:config.addText,
                 onclick:function(){
                    ret.ADD_inp();
                 }
              }));
           if(config.beforeOrAfter){
              _el.APPEND(ret.inp, ret.listCatcher=_el.CREATE('div', RMFid(config, "listCatcher"), RMFclass(config,"RMFdynamicListCatcher","dynamicListCatcher")));
           }
         }
      })(),
      "section"//labelType
   ),
   "singleLine":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFsingleLine",
        placeHolder:""
      },/// rmfdefault
      {},// rmfspecials
      function(){return this.inp.value;},// COLL
      function(v){this.inp.value=v;},//SET
      function(config,ret){
        // console.log(config);
         ret.inp=_el.CREATE("input", RMFid(config, "singleLine"), RMFclass(config,"RMFsingleLine","singleLine"), {
           name:RMFgetName(config), 
           placeholder: config.placeHolder,
           value:config.default
         }); 
      },
      "label"//labelType
   ),
   "checkbox":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFcheckbox",
        default:false
      },/// rmfdefault
      {default:"rawBool"},// rmfspecials
      function(){return this.inp.checked;},// COLL
      function(v){this.inp.checked=!!v;},//SET
      function(config,ret){
         var val = RMFgetValue(config);
        // alert(JSON.stringify(config));
         ret.inp=_el.CREATE('input',RMFid(config, "checkbox"), RMFclass(config,"RMFcheckbox","checkbox"),{
          type:"checkbox",
          checked:config.default,
          name:RMFgetName(config)
         });
         // alert(JSON.stringify(val));
         if(val !== ""){
            ret.inp.value=val;
            ret.inp.obVal=val;
         }
      //    alert(RMFgetName(config)+" "+JSON.stringify(config));
      },
      "label"//labelType
   ),
   "checkFamily":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for radio Input
            id: id for radio input ("")
            class:class for radio ("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFcheckFamily",
        inps:[] // a list of inps objects for the checkboxes in the family minus the name attribute with an extra 'value' member
       
      },/// rmfdefault
      {},// rmfspecials
      function(){
        var ret=[];
        for(var i=0; i<this.inps.length; i++)
        {
           if(this.inps[i].inp.checked === true){
              ret.push(this.inps[i].inp.obVal);
              //console.log(this.inps[i].inp.value);
           }
        }
        return ret;
      },// COLL
      function(v){
        v=v || [];
        var inf={};
        for(var i=0; i<v.length && v!=="all"; i++)
        {
           inf[v[i]]=true;
        }
        for(var i=0; i<this.inps.length; i++)
        {
           if( v === "all" || inf[this.inps[i].inp.value]){
              this.inps[i].inp.checked=true;
              continue;
           }this.inps[i].inp.checked=false;
        }
      },//SET
      function(config,ret){
         ret.inp=_el.CREATE('div',RMFid(config, "checkFamily"), RMFclass(config,"RMFcheckFamily","checkFamily"),{});
         ret.inps=[];
         for(var i=0; i<config.inps.length; i++)
         {
            var temp=this.checkbox(_ob.COMBINE(config.inps[i], {classRunner:config.classRunner,nameStack:config.nameStack, name:RMFconcatName(config.name, config.inps[i].value)}));
            _el.APPEND(ret.inp, temp.el);
            ret.inps.push(temp);
         }
      },
      "section"//labelType
   ),
   "radio":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for radio Input
            id: id for radio input ("")
            class:class for radio ("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFradio",
        default:false,
        onchange:""
      },/// rmfdefault
      {default:"rawBool"},// rmfspecials
      function(){if(this.inp.checked){return this.inp.value;}return undefined;},// COLL
      function(v){
        if(v === this.inp.value){this.inp.checked=true; return;}this.inp.checked=false;
      },//SET
      function(config,ret){
         ret.inp=_el.CREATE('input',RMFid(config, "radio"), RMFclass(config,"RMFradio","radio"),{
           type:"radio",
           checked:config.default,
           name:RMFgetName(config),
           onchange:config.onchange,
           value:RMFgetValue(config)
         });
      },
      "label"//labelType
   ),
   "radioFamily":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
          classRunner:"RMFradioFamily",
          inps:[],//  inps:a list of inp configs for the 'radio' inp type minus 'name' 'id' and 'class',
           onchange:""
      },/// rmfdefault
      {},// rmfspecials
      function(){
             var hol;
             for(var i=0; i<this.inps.length; i++)
             {
               if(typeof (hol=this.inps[i].COLL()) !== "undefined"){
                  return hol;
               }
             }return "";
      },// COLL
      function(v){
         for(var i=0; i<this.inps.length; i++)
         {
            this.inps[i].SET(v);
         }
      },//SET
      function(config,ret){
          ret.inps=[];
          ret.inp=_el.CREATE('div',RMFid(config, "radioFamily"), RMFclass(config,"RMFradioFamily","radioFamily"),{});
          for(var i=0; i<config.inps.length; i++)
          {
            var temp;
             ret.inps.push(temp=this.radio(_ob.COMBINE(config.inps[i],{
                name:config.name,
                onchange:config.onchange,
                nameStack:config.nameStack,
                classRunner:config.classRunner
             })));     
             if(config.default === temp.value){
                temp.checked=true;
             }
             _el.APPEND(ret.inp, temp.el);
          }
      },
      "section"//labelType
   ),
   "select":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFselect",
        inps:[], // opts: an array of objcts for creating options {value: "", labelText:""}
      },/// rmfdefault
      {},// rmfspecials
      function(){
        if(this.inp.options.length && this.inp.options[this.inp.selectedIndex]){
           return this.inp.options[this.inp.selectedIndex].value;
        }return null;
      },// COLL
      function(v){
        for(var i=0; i<this.inp.options.length; i++)
        {
          if(this.inp.options[i].value === v){
             this.inp.selectedIndex=i;
				 this.inp.dispatchEvent(new Event('change'));
			 break;
          }
        }
      },//SET
      function(config,ret){
         
         var optsList=[];
         var selectedIndex=0;
		 var opt;
		 ret.CHANGE_options=function(arr){
			 Array.from(ret.inp.children).forEach(function(a){
					 _el.REMOVE(a);
			 });
			 arr.forEach(function(a){
				 _el.APPEND(ret.inp, _el.CREATE('option','','',{value:a.value},[a.labelText]));
			 });
		 }
         for(var i=0; i<config.inps.length; i++)
         {
            if((!i && !config.default)){
                config.default=config.inps[i].value;
            }
            optsList.push(opt=_el.CREATE('option',RMFid(config, "option"), RMFclass(config,"RMFoption","option"),{value:RMFgetValue(config.inps[i])},[RMFgetCard(config.inps[i],0)]));
			if(config.inps[i].class){opt.className+=' '+config.inps[i].class;}
            if(config.inps[i].value === config.default){
               selectedIndex=i;
            }
         }
         ret.inp=_el.CREATE("select",RMFid(config, "select"), RMFclass(config,"RMFselect","select"),
		 _ob.COMBINE({
           name:RMFgetName(config),
           selectedIndex:selectedIndex
         },config.selectInsert || {}),
		 optsList);
		 ret.inp.selectedIndex=selectedIndex;
      },
      "label"//labelType
   ),
   "distinct":function(config){
      config=config || {};
      RMFdefaults(config, {
        distinctMode:"radio"
      });
      switch(config.distinctMode){
         case "radio":
           return this.radioFamily(config);
           break;
         case "select":
           return this.select(config);
           break;
      }
   },
   "selectToNew":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFselectToNew",
        selectConfig:{},// selectConfig: an object to be passed
        dynamicConfig:{},// a config to be passed to the single line inp
      },/// rmfdefault
      {},// rmfspecials
      function(){
        var selVal=this.selectInp.COLL();
        if(selVal === "--NEW--"){
           return this.dynamicInp.COLL();
        } 
        return selVal;
      },// COLL
      function(v){
         for(var i=0; i<this.selectInp.inp.options.length; i++)
         {
           // alert("yo");
           // alert(v+" - "+this.selectInp.inp.options[i].value);
            if(this.selectInp.inp.options[i].value == v){
                this.selectInp.SET(v);
               // alert("hey");
                return;
            }
         }
         this.selectInp.inp.selectedIndex=0;
         this.selectInp.inp.onchange.call(this.selectInp.inp);
         this.dynamicInp.SET(v); 
         
      },//SET
      function(config,ret){
         if(config.inps[0].value !== "--NEW--"){
            if(!("default" in config.selectConfig)){
               config.selectConfig.default=config.inps[0].value;
                //alert(config.selectConfig.inps[0].value);
            }
            config.inps.unshift({
              value:"--NEW--",
              labelText:"New"
            });
         }
         
		 ret.CHANGE_options=function(arr){
			 Array.from(ret.selectInp.inp.children).forEach(function(a){
				 if(a.value && a.value !== '--NEW--'){
					 _el.REMOVE(a);
				 }
			 });
			 arr.forEach(function(a){
				 _el.APPEND(ret.selectInp.inp, _el.CREATE('option','','',{value:a.value},[a.labelText]));
			 });
		 }
         ret.inp=_el.CREATE("div", RMFid(config, "selectToNew"), RMFclass(config,"RMFselectToNew","selectToNew"), {}); 
         config.dynamicConfig.name=config.name+"-NEW";
         config.dynamicConfig.nameStack=config.nameStack;
         ret.dynamicInpFunct=this.CURRY(config.dynamicConfig.type || "singleLine");
         var temp;
         _el.APPEND(ret.inp,[
            (temp=this.select(_ob.COMBINE(config.selectConfig,{
             name:config.name, inps:config.inps, nameStack:config.nameStack, classRunner:config.classRunner
            }))).el,
            ret.dynCatcher=_el.CREATE('div',RMFid(config, "selectToNewCatcher"), RMFclass(config,"RMFselectToNewCatcher","selectToNewCatcher"))
         ]);
         ret.selectInp=temp;
         temp.inp.onchange=function(){
           _el.EMPTY(ret.dynCatcher);
           if(this.options[this.selectedIndex].value === "--NEW--"){
              var temp=ret.dynamicInpFunct(config.dynamicConfig);
              _el.APPEND(ret.dynCatcher, temp.el);
              ret.dynamicInp=temp;
           }
         }
      },
      "label"//labelType
   ),
   "radioToNew":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFradioToNew",
        radioFamilyConfig:{},
        dynamicConfig:{}
        
      },/// rmfdefault
      {},// rmfspecials
      function(){
        var ret=this.radioInp.COLL();
        if(ret === "--NEW--"){
           ret=this.dynamicInp.COLL();
        }return ret;
      },// COLL
      function(v){
        for(var i=0; i<this.radioInp.inps.length; i++)
        {
           if(this.radioInp.inps[i].inp.value === v){
              this.radioInp.inps[i].inp.checked=true;
              return;
           }
        }
        this.radioInp.inps[i-1].inp.checked=true;
        this.radioInp.inps[i-1].inp.onchange.call(this.radioInp.inps[i-1].inp);
        this.dynamicInp.SET(v);
      },//SET
      function(config,ret){
         if(config.inps[config.inps.length-1].value !== "--NEW--"){
            if(!("default" in config.radioFamilyConfig)){
               config.radioFamilyConfig.default=config.inps[0].value;
                //alert(config.selectConfig.inps[0].value);
            }
            config.inps.push({
              value:"--NEW--",
              labelText:"New"
            });
         }
         ret.inp=_el.CREATE("div", RMFid(config, "radioToNew"), RMFclass(config,"RMFradioToNew","radioToNew"), {}); 
        
         var temp;
         _el.APPEND(ret.inp,[
            (temp=this.radioFamily(_ob.COMBINE(config.radioFamilyConfig, {nameStack:config.nameStack,name:config.name, inps:config.inps,
               onchange:function(){
                //alert("hey");
               _el.EMPTY(ret.dynCatcher);
                //console.log(this);
               if(this.value === "--NEW--"){
                 // alert("yo");
                  console.log(ret.dynCatcher);
                  var temp=ret.dynamicInpFunct(config.dynamicConfig);
                  //console.log(temp)
                  _el.APPEND(ret.dynCatcher, temp.el);
                  ret.dynamicInp=temp;
                  //ret.dynCatcher.style.border="3px solid red";
                  //console.log(ret.dynCatcher.children);
               }
             }
            }))).el,
            ret.dynCatcher=_el.CREATE('div',RMFid(config, "radioToNewCatcher"), RMFclass(config,"RMFradioToNewCatcher","radioToNewCatcher"))
         ]);
         ret.radioInp=temp;
         config.dynamicConfig.name=config.name+"-NEW";
         config.dynamicConfig.nameStack=config.nameStack;
         ret.dynamicInpFunct=this.CURRY(config.dynamicConfig.type || 'singleLine');
         
         
      },
      "section"//labelType
   ),
   "paragraph":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFparagraph",
        placeHolder:"", // placeHolder: a text to hold the place in the input
      },/// rmfdefault
      {},// rmfspecials
      function(){return this.inp.value;},// COLL
      function(v){this.inp.value=v;},//SET
      function(config,ret){
         var attributes={};
		 if(typeof config.spellCheck !== "undefined"){
			 if(!config.spellCheck){
				 attributes.spellcheck="false";
			 }
		 }
		 if(typeof config.wordWrap !== "undefined"){
			 if(!config.wordWrap){
				 attributes.wrap="off";
			 }
		 }
         ret.inp=_el.CREATE("textarea", RMFid(config, "paragraph"), RMFclass(config,"RMFparagraph","paragraph"), {
           name:RMFgetName(config), 
           placeholder: config.placeHolder,
           value:config.default,
		   attributes:attributes
		   
         }); 
      },
      "section"//labelType
   ),
   "shark":RMFbasicMultiInput({
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
            preInps: any arguments you want to send in the config of the inps by name ({})
        */
    },//rmfdefault 
    {},//rmfspecials 
    function(a){//coll
      return a;
    }, [//inpList
      {
        type:"singleLine",
        name:"name",
        labelText:"Name"
      },
      {
        type:"select",
        labelText:"Species",
        name:"species",
        inps:[
           {
              value:"greatWhite",
              labelText:"Great White"
           },
           {
              value:"hammer",
              labelText:"Hammer Head"
           }
        ]
      }
    ]),
    "dynamicOnRadio":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFdynamicOnRadio",
        dynamicForms:{}, // placeHolder: a text to hold the place in the input
        radioFamilyConfig:{}, 
        
      },/// rmfdefault
      {},// rmfspecials
      function(){
         var nam=this.radioFamilyInp.COLL();
         var ret={};
         ret[nam]=this.dynamicFormColl.COLL();
         return ret;
      },// COLL
      function(v){
         v=v || {};
         for(var mem in v)
         {
            this.radioFamilyInp.SET(mem);
            this.RADCHANGE();
            this.dynamicFormColl.SET(v[mem]);
         }
      },//SET
      function(config,ret){
         config.radioFamilyConfig.inps=[];
         config.radioFamilyConfig.name=config.name
         config.radioFamilyConfig.nameStack=config.nameStack;
         for(var mem in config.dynamicForms)
         {
            config.radioFamilyConfig.inps.push({
              labelText:config.dynamicForms[mem].labelText,
              value:mem
            });
         }
         function CHANGE(){
            var fo={};
            ret.dynamicFormColl=new FORM_COL_OB(fo);
            var cat=ret.radioFamilyInp.COLL();
            if(cat){
               MAKE(ret.dynamicContainer, config.dynamicForms[cat].form, fo, RMFconcatName(RMFconcatName(config.nameStack,config.name), cat), config.dynamicForms[cat].config || false);
            }else{
               _el.EMPTY(ret.dynamicContainer);
            }
         } 
         ret.RADCHANGE=CHANGE;
         ret.inp=_el.CREATE("div", RMFid(config, "dynamicOnRadio"), RMFclass(config,"RMFdynamicOnRadio","dynamicOnRadio"), {},[
            ret.radioContainer=_el.CREATE('div',RMFid(config, "dynamicOnRadioRadioContainer"), RMFclass(config,"RMFdynamicOnRadioRadioContainer","dynamicOnRadioRadioContainer"),{
               onchange:function(){
                 CHANGE();
               }
            }),
            ret.dynamicContainer=_el.CREATE('div',RMFid(config, "dynamicOnRadioDynamicContainer"), RMFclass(config,"RMFdynamicOnRadioDynamicContainer","dynamicOnRadioDynamicContainer"))
         ]); 

         ret.radioFamilyInp=this.radioFamily(config.radioFamilyConfig);
         _el.APPEND(ret.radioContainer, ret.radioFamilyInp.el);
         CHANGE();
      },
      "section"//labelType
   ),
    "dynamicOnRadioFallthrough":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFdynamicOnRadioFallthrough",
        dynamicInps:{}, // placeHolder: a text to hold the place in the input
        radioFamilyConfig:{}, 
        
      },/// rmfdefault
      {},// rmfspecials
      function(){
         var nam=this.radioFamilyInp.COLL();
         var ret={};
         if(typeof this.dynamicInpColl.COLL !== "function"){
            return {};
         }
         return this.dynamicInpColl.COLL();
         return ret;
      },// COLL
      function(v){
         v=v || {};
         for(var mem in v)
         {
            this.radioFamilyInp.SET(mem);
            this.RADCHANGE();
            this.dynamicInpColl.SET(v[mem]);
         }
      },//SET
      function(config,ret){
         config.radioFamilyConfig.inps=[];
         config.radioFamilyConfig.name=config.name
         config.radioFamilyConfig.nameStack=config.nameStack;
         for(var mem in config.dynamicInps)
         {
            config.radioFamilyConfig.inps.push({
              labelText:config.dynamicInps[mem].labelText,
              value:mem
            });
         }
         function CHANGE(){
            var fo={};
            ret.dynamicFormColl=new FORM_COL_OB(fo);
            var cat=ret.radioFamilyInp.COLL();
            var dynamicInp=config.dynamicInps[cat];
            _el.EMPTY(ret.dynamicContainer);
            if(dynamicInp){
               ret.dynamicInpColl=INPTYPES[dynamicInp.inpType](_ob.COMBINE(dynamicInp.config, {nameStack:config.nameStack, name:config.name}));
               _el.APPEND(ret.dynamicContainer, ret.dynamicInpColl.el);
            }else{
               _el.EMPTY(ret.dynamicContainer);
            }
         } 
         ret.RADCHANGE=CHANGE;
         ret.inp=_el.CREATE("div", RMFid(config, "dynamicOnRadioFallthrough"), RMFclass(config,"RMFdynamicOnRadio","dynamicOnRadio"), {},[
            ret.radioContainer=_el.CREATE('div',RMFid(config, "dynamicOnRadioRadioContainer"), RMFclass(config,"RMFdynamicOnRadioRadioContainer","dynamicOnRadioRadioContainer"),{
               onchange:function(){
                 CHANGE();
               }
            }),
            ret.dynamicContainer=_el.CREATE('div',RMFid(config, "dynamicOnRadioDynamicContainer"), RMFclass(config,"RMFdynamicOnRadioDynamicContainer","dynamicOnRadioDynamicContainer"))
         ]); 

         ret.radioFamilyInp=this.radioFamily(config.radioFamilyConfig);
         _el.APPEND(ret.radioContainer, ret.radioFamilyInp.el);
         CHANGE();
      },
      "section"//labelType
   ),
    "dynamicOnSelect":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFdynamicOnSelect",
        dynamicForms:{}, // placeHolder: a text to hold the place in the input
        selectConfig:{}, 
        
      },/// rmfdefault
      {},// rmfspecials
      function(){
         var nam=this.selectInp.COLL();
         var ret={};
         ret[nam]=this.dynamicFormColl.COLL();
         return ret;
      },// COLL
      function(v){
         v=v || {};
         for(var mem in v)
         {
            this.selectInp.SET(mem);
            this.RADCHANGE();
            this.dynamicFormColl.SET(v[mem]);
         }
      },//SET
      function(config,ret){
         config.selectConfig.inps=[];
         config.selectConfig.name=config.name
         config.selectConfig.nameStack=config.nameStack;
         for(var mem in config.dynamicForms)
         {
            config.selectConfig.inps.push({
              labelText:config.dynamicForms[mem].labelText,
              value:mem
            });
         }
         function CHANGE(){
            var fo={};
            ret.dynamicFormColl=new FORM_COL_OB(fo);
            var cat=ret.selectInp.COLL();
            if(cat){
               MAKE(ret.dynamicContainer, config.dynamicForms[cat].form, fo, RMFconcatName(RMFconcatName(config.nameStack,config.name), cat), config.dynamicForms[cat].config || false);
            }else{
               _el.EMPTY(ret.dynamicContainer);
            }
         } 
         ret.RADCHANGE=CHANGE;
         ret.inp=_el.CREATE("div", RMFid(config, "dynamicOnSelect"), RMFclass(config,"RMFdynamicOnSelect","dynamicOnSelect"), {},[
            ret.selectContainer=_el.CREATE('div',RMFid(config, "dynamicOnSelectSelectContainer"), RMFclass(config,"RMFdynamicOnSelectSelectContainer","dynamicOnSelectSelectContainer"),{
               onchange:function(){
                 CHANGE();
               }
            }),
            ret.dynamicContainer=_el.CREATE('div',RMFid(config, "dynamicOnSelectDynamicContainer"), RMFclass(config,"RMFdynamicOnSelectDynamicContainer","dynamicOnSelectDynamicContainer"))
         ]); 

         ret.selectInp=this.select(config.selectConfig);
         _el.APPEND(ret.selectContainer, ret.selectInp.el);
         CHANGE();
      },
      "section"//labelType
   ),
   "dynamicOnSelectFallthrough":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFdynamicOnSelectFallthrough",
        dynamicInps:{},
        selectConfig:{}
        
      },/// rmfdefault
      {},// rmfspecials
      function(){
         var nam=this.selectInp.COLL();
         var ret={};
         if(typeof this.dynamicInpColl.COLL !== "function"){return {};}
         return this.dynamicInpColl.COLL();
         return ret;
      },// COLL
      function(v){
         v=v || {};
         for(var mem in v)
         {
            this.selectInp.SET(mem);
            this.RADCHANGE();
            this.dynamicInpColl.SET(v[mem]);
         }
      },//SET
      function(config,ret){
         config.selectConfig.inps=[];
         config.selectConfig.name=config.name;
         config.selectConfig.nameStack=config.nameStack;
         for(var mem in config.dynamicInps)
         {
            config.selectConfig.inps.push({
              labelText:config.dynamicInps[mem].labelText,
              value:mem
            });
         }
         function CHANGE(){
            _el.EMPTY(ret.dynamicContainer);
            var cat=ret.selectInp.COLL();
            var dynamicInp=config.dynamicInps[cat];
            if(dynamicInp){
               ret.dynamicInpColl=INPTYPES[dynamicInp.inpType](_ob.COMBINE(dynamicInp.config || {} , {name:config.name, formCol:config.formCol, nameStack:config.nameStack}));
               _el.APPEND(ret.dynamicContainer,ret.dynamicInpColl.el);
            }else{
               _el.EMPTY(ret.dynamicContainer);
            }
         } 
         ret.RADCHANGE=CHANGE;
         ret.inp=_el.CREATE("div", RMFid(config, "dynamicOnSelectFallthrough"), RMFclass(config,"RMFdynamicOnSelectFallthrough","dynamicOnSelectFallthrough"), {},[
            ret.selectContainer=_el.CREATE('div',RMFid(config, "dynamicOnSelectFallthroughSelectContainer"), RMFclass(config,"RMFdynamicOnSelectFallthroughSelectContainer","dynamicOnSelectFallthroughSelectContainer"),{
               onchange:function(){
                 CHANGE();
               }
            }),
            ret.dynamicContainer=_el.CREATE('div',RMFid(config, "dynamicOnSelectFallthroughDynamicContainer"), RMFclass(config,"RMFdynamicOnSelectFallthroughDynamicContainer","dynamicOnSelectFallthroughDynamicContainer"))
         ]); 

         ret.selectInp=this.select(config.selectConfig);
         _el.APPEND(ret.selectContainer, ret.selectInp.el);
         CHANGE();
      },
      "section"//labelType
   ),
   "dynamicOnCheckbox":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFdynamicOnCheckbox",
        checkboxConfig:{},
        dynamicForm:[],
        dynamicFormConfig:{}
         
      },/// rmfdefault
      {},// rmfspecials
      function(){
         if(this.checkboxInp.COLL()){
            return this.dynamicFormColl.COLL();
         }return false;
      },// COLL
      function(a){
         if(a){
            this.checkboxInp.SET(true);
            this.REFCHANGE();
            this.dynamicFormColl.SET(a);
         }else{
            this.checkboxInp.SET(false);
            this.REFCHANGE();
         }
      },//SET
      function(config,ret){
        // console.log(config);
         function CHANGE(){
            _el.EMPTY(ret.dynamicContainer);
            var fo={};
            ret.dynamicFormColl=new FORM_COL_OB(fo);
            var cat=ret.checkboxInp.COLL();
            _el.EMPTY(ret.dynamicContainer);
            if(cat){
               MAKE(ret.dynamicContainer, config.dynamicForm, fo, RMFconcatName(config.nameStack,config.name), config.dynamicFormConfig || false);
            }
         }
         ret.REFCHANGE=CHANGE;
         ret.inp=_el.CREATE("div", RMFid(config, "dynamicOnCheckbox"), RMFclass(config,"RMFdynamicOnCheckbox","dynamicOnCheckbox"), {}); 
         ret.checkboxInp=this.checkbox(_ob.COMBINE(config.checkboxConfig,{name:config.name,nameStack:config.nameStack, formCol:config.formCol}));
         ret.checkboxInp.inp.onchange=function(){CHANGE();}
         _el.APPEND(ret.inp, [
            ret.checkboxInp.el,
            ret.dynamicContainer=_el.CREATE('div',RMFid(config, "dynamicOnCheckboxDynamicContainer"), RMFclass(config,"RMFdynamicOnCheckboxDynamicContainer","dynamicOnCheckboxDynamicContainer"),{})
         ]);
         CHANGE();
      },
      "section"//labelType
   ),
   "dynamicOnCheckboxFallthrough":RMFbasicInput(
      {
        /*COMES WITH THESE BY DEFAULT*/
        /*
          name:name for Input
            id: id for input ("")
            class:class for input("")
            default: default setting ("")
            labelText: text for label ("")
            labelSequence: whether the label text should appear before or after the input (true)
            errorText: text for error ("")
            errorSequence: whether the error text should appear before or after the input (false)
        */
        classRunner:"RMFdynamicOnCheckboxFallthrough",
        checkboxConfig:{},
        inpType:"singleLine",
        inpTypeConfig:{}
         
      },/// rmfdefault
      {},// rmfspecials
      function(){
         if(this.checkboxInp.COLL()){
           if(this.dynamicInp.COLL){
            return this.dynamicInp.COLL();
           }
         }return false;
      },// COLL
      function(a){
         if(a){
            this.checkboxInp.SET(true);
            this.REFCHANGE();
            this.dynamicInp.SET(a);
         }else{
            this.checkboxInp.SET(false);
            this.REFCHANGE();
         }
      },//SET
      function(config,ret){
         //console.log("cbf beg ", config);
         config.inpTypeConfig.formCol=config.formCol;
         config.inpTypeConfig.name=config.inpTypeConfig.name || config.name;
         config.inpTypeConfig.nameStack=config.nameStack;
         function CHANGE(){
            _el.EMPTY(ret.dynamicContainer);
            var cat=ret.checkboxInp.COLL();
            _el.EMPTY(ret.dynamicContainer);
            ret.dynamicInp=false;
            if(cat){
               //console.log("from checkbox fallthrough ", config.inpTypeConfig, config.inpType);
               ret.dynamicInp=INPTYPES[config.inpType](config.inpTypeConfig);
               _el.APPEND(ret.dynamicContainer, ret.dynamicInp.el);
            }
         }
         ret.REFCHANGE=CHANGE;
         ret.inp=_el.CREATE("div", RMFid(config, "dynamicOnCheckboxFallthrough"), RMFclass(config,"RMFdynamicOnCheckboxFallthrough","dynamicOnCheckboxFallthrough"), {}); 
         ret.checkboxInp=this.checkbox(_ob.COMBINE(config.checkboxConfig,{name:config.name,nameStack:config.nameStack, formCol:config.formCol}));
         ret.checkboxInp.inp.onchange=function(){CHANGE();}
         _el.APPEND(ret.inp, [
            ret.checkboxInp.el,
            ret.dynamicContainer=_el.CREATE('div',RMFid(config, "dynamicOnCheckboxFallthroughDynamicContainer"), RMFclass(config,"RMFdynamicOnCheckboxFallthroughDynamicContainer","dynamicOnCheckboxFallthroughDynamicContainer"),{})
         ]);
         CHANGE();
      },
      "section"//labelType
   ),
   "button":function(config){
      /*config
          text: text to be in button
          action: function to fire on button press
          class:
          id:
      */
      var ret={
         el:_el.CREATE('input',RMFid(config, ""), RMFclass(config,"RMFbutton","button"),{
            type:'button',
            onclick:function(e){config.action(this, e);},
            value:config.text
         },[]),
         action:config.action
      };
      return ret;
   },
   "hidden":function(config){
      /*
          value:,
          name:
          class:
          id
      */
       config=config || {};
       
       return {
          el:_el.CREATE('input',RMFid(config, ""), RMFclass(config,"RMFhidden","'hidden"),{name:config.name, type:"hidden", value:config.value}),
          COLL:function(){return config.value;},
          SET:function(a){config.value=a;}
       };
   },
   "dummy":function(config){
      return _ob.COMBINE({
         
          el:_el.CREATE('input',RMFid(config, ""), RMFclass(config,"RMFhidden","'hidden"),{name:config.name, type:"hidden", value:config.value}),
      },config);
   },
   "hider":function(config){
      config=config || {};
      function OPEN(){
          STATE=1;
          ret.labelTextBox.innerHTML=config.labelText+" ";
          ret.inp.className=ret.inp.className.replace(/RMFhiderClosed/g).trim();
          _el.APPEND(ret.labelTextBox, _el.CREATE('input','','RMFhiderButton',{type:"button",value:"^"}));
      };
      function CLOSE(){
         STATE=0;
         ret.labelTextBox.innerHTML=config.labelText+" ";
         _el.APPEND(ret.labelTextBox, _el.CREATE('input',"","RMFhiderButton",{type:"button", value:"V"}));
         ret.inp.className+=" RMFhiderClosed";
      };
      var STATE=0;
      function TOGGLE(){
         if(STATE){
            return CLOSE();
         }return OPEN();
      }
      
      var ret=this.section(config);

      
      ret.labelTextBox.onclick=function(){TOGGLE();}
      ret.labelTextBox.className+=" RMFhiderLabel";
      CLOSE();

      return ret;
   },
   "compoundHider":function(config){
      config=config || {};
      function OPEN(){
          STATE=1;
          ret.labelTextBox.innerHTML=config.labelText+" ";
          ret.inp.className=ret.inp.className.replace(/RMFhiderClosed/g).trim();
          _el.APPEND(ret.labelTextBox, _el.CREATE('input','','RMFhiderButton',{type:"button",value:"^"}));
      };
      function CLOSE(){
         STATE=0;
         ret.labelTextBox.innerHTML=config.labelText+" ";
         _el.APPEND(ret.labelTextBox, _el.CREATE('input',"","RMFhiderButton",{type:"button", value:"V"}));
         ret.inp.className+=" RMFhiderClosed";
      };
      var STATE=0;
      function TOGGLE(){
         if(STATE){
            return CLOSE();
         }return OPEN();
      }
      
      var ret=this.compound(config);

      
      ret.labelTextBox.onclick=function(){TOGGLE();}
      ret.labelTextBox.className+=" RMFhiderLabel";
      CLOSE();

      return ret;
   },
   "sectionHider":function(config){
      config=config || {};
      function OPEN(){
          STATE=1;
          ret.labelTextBox.innerHTML=config.labelText+" ";
          ret.inp.className=ret.inp.className.replace(/RMFhiderClosed/g).trim();
          _el.APPEND(ret.labelTextBox, _el.CREATE('input','','RMFhiderButton',{type:"button",value:"^"}));
      };
      function CLOSE(){
         STATE=0;
         ret.labelTextBox.innerHTML=config.labelText+" ";
         _el.APPEND(ret.labelTextBox, _el.CREATE('input',"","RMFhiderButton",{type:"button", value:"V"}));
         ret.inp.className+=" RMFhiderClosed";
      };
      var STATE=0;
      function TOGGLE(){
         if(STATE){
            return CLOSE();
         }return OPEN();
      }
      
      var ret=this.section(config);

      
      ret.labelTextBox.onclick=function(e){e.preventDefault(); e.cancelBubble=true;TOGGLE();}
      ret.labelTextBox.className+=" RMFhiderLabel";
      CLOSE();

      return ret;
   }
};

INPTYPES_inpLists={
    'TBL_jipper':[
        {
           'type':'singleLine',
            name:'yo',
            labelText:'YYou'
        }
    ]
};
INPTYPES.TBL_jipper= RMFbasicMultiInput('', '', '', INPTYPES_inpLists.TBL_jipper, '');


function MODAL_NOW(){
   var ret={
       CLOSE:function(){
          _el.REMOVE(this.parent);
       }
   };
   ret.parent=_el.CREATE("div","","basicModalParent",{},[
      ret.backer=_el.CREATE("div","","basicModalBacker",{},[]),
      ret.client=_el.CREATE("div","","basicModalClient",{},[]),
      ret.closer=_el.CREATE('div','','basicModalCloser',{
        onclick:function(){ret.CLOSE();}
      },[_el.TEXT('X')])
   ]);
   _el.APPEND(el_body, ret.parent);
   return ret;
}

function TableSearchAdd(inpRet,config,forEdit){
   var mod=MODAL_NOW();
   var catcher;
   var dd;
   if(config.addApName){
      dd=new dFCM('RMF/'+config.typeName+'/action.php?action='+config.addApName, {method:'POST', headers:{'Content-Type':'special/obPost'}});
   }else{
      dd=new dFCM(config.addFile || 'php_core/globalAdd.php?typeName='+config.typeName, {method:'POST', headers:{'Content-Type':'special/obPost'}});
   }
   var inpL=[];
   if(config.inpList){
      inpL=config.inpList;
   }else if(config.addApName){
       inpL=INPTYPES_inpLists[config.typeName+config.addApName];
   }else{
      inpL=INPTYPES_inpLists[config.typeName];
   }
    var FO=RMFORM(
     mod.client,
     inpL,
     config.typeName+(config.addApName || 'add'),
     {
        ajaxDacm:dd,
        ajaxProc:function(res){
           _el.EMPTY(catcher);
           if(res === "SUCCESS"){
                 
              
              var temp=CARDTYPES.GET(config.cardName || config.typeName, FO.COLL(), 2);
              temp.className+=' tableAddResult';

              temp.onclick=function(){
                 inpRet.SET(FO.COLL());
                 mod.CLOSE();
              }
              _el.APPEND(catcher,temp);
           }else{
              _el.APPEND(catcher, _el.TEXT(res));
           }

           
        }
     }
   );
   _el.APPEND(mod.client,
      catcher=_el.CREATE("div","",'',{},[
         
      ])
   );
}

function TableSearchSearch(inpRet, config, forEdit){
   var mod = MODAL_NOW();
   var catcher;
   var dd;
   if(config.apName){
      dd=new dFCM('RMF/'+config.typeName+'/action.php?action='+config.apName, {method:'POST', headers:{'Content-Type':'special/obPost'}});
   }else{
      dd=new dFCM(config.searchFile || 'php_core/globalSearch.php?typeName='+config.typeName, {method:'POST', headers:{'Content-Type':'special/obPost'}});
   }
   var inpL=[];
   if(config.inpList){
      inpL=config.inpList;
   }else if(config.apName){
       inpL=INPTYPES_inpLists[config.typeName+config.apName];
   }else{
      inpL=INPTYPES_inpLists[config.typeName];
   }
   RMFORM(
     mod.client,
     inpL,
     config.typeName+(config.apName || 'search'),
     {
        ajaxDacm:dd,
        ajaxProc:function(res){
           _el.EMPTY(catcher);
           res=JSON.parse(res);
           
           for(var i=0; i<res.length; i++)
           {
              var temp=CARDTYPES.GET(config.cardName || config.typeName, res[i], 2);
              temp.className+=' tableSearchResult';
              temp.DATA_res=res[i];
              temp.onclick=function(){
                 inpRet.SET(this.DATA_res);
                 mod.CLOSE();
              }
              _el.APPEND(catcher,temp);
           }

           
        }
     }
   );
   _el.APPEND(mod.client,
      catcher=_el.CREATE("div","",'',{},[
         
      ])
   );
}
function TableSearchAp(apName, inpRet, config){
   var mod = MODAL_NOW();
   var catcher;
   var dd;
   var FO=RMF_actionProcedureForm(apName, config.typeName,mod.client, "mod"+config.typeName+apName, {
      ajaxProc:function(res){
         if(res === "SUCCESS"){
            alert("success");
            inpRet.SET(FO.COLL());
            mod.CLOSE();
         }else{
             var tRes;
             try{
               tRes=JSON.parse(res);
               inpRet.SET(res);
               mod.CLOSE();
             }catch(e){
               _el.EMPTY(catcher); 
               _el.APPEND(catcher, _el.TEXT(res));
             }
         }
      }
   });
  
   _el.APPEND(mod.client,
      catcher=_el.CREATE("div","",'',{},[
         
      ])
   );
}
INPTYPES.tableSearch=function(config){
   var ret;
   if(config.inps){
      ret=this.compound(_ob.COMBINE(config,{
         class:'tableSearch'
      }));
   }else{
      ret= this[config.typeName](_ob.COMBINE(config,{class:'tableSearch'}));
   }
   ret.el.insertBefore(_el.CREATE('input','','',{type:'button', value:'Search', onclick:function(){
      TableSearchSearch(ret,config);
   }}), ret.el.children[0]);

   if(config.includeAdd){
      ret.el.insertBefore(_el.CREATE('input','','',{type:'button', value:'Add', onclick:function(){
         TableSearchAdd(ret,config);
      }}), ret.el.children[0]);
   }

   if(config.apButtons){
      config.apButtons.forEach(function(a){
         ret.el.insertBefore(_el.CREATE('input','','',{type:'button',value:'add', onclick:function(){
            TableSearchAp(a, ret,config);
         }}),ret.el.children[0])
      });
   }
   ret.oldCOLL=ret.COLL;
   ret.COLL=function(){
	   var r=this.oldCOLL();
	   var found=false;
	   for(var mem in r)
	   {
		   if(typeof r[mem] !== 'boolean' && r[mem]){found=true; break;}
	   }
	   if(!found){return {};}
	   return r;
   }
   return ret;
}

INPTYPES.tableSearchForEdit=function(config){   
   var ret;
   /*if(config.inps){
      ret=this.compound(_ob.COMBINE(config,{
         class:'tableSearch'
      }));
   }else{
      ret= this[config.typeName](_ob.COMBINE(config,{class:'tableSearch'}));
   }
   ret.el.insertBefore(_el.CREATE('input','','',{type:'button', value:'Search', onclick:function(){
      TableSearchSearch(ret,config,true);
   }}), ret.el.children[0]);*/
   ret=this.tableSearch(config);
   ret.TABLESEARCHFOREDITOLDSET=ret.SET;
   ret.SET=function(v){
      if(v){
        v.OGvalue=_ob.CLONE(v);
      }
      ret.TABLESEARCHFOREDITOLDSET(v);
   }
   return ret;
   
}
INPTYPES.tableEditBasic=function(config){
   var ret=this.section(config);
   /*ret.TABLEEDITBASICSET=ret.SET;
   ret.SET=function(v){
      if(v){
         v.OGvalue=_ob.CLONE(v);
      }
      this.TABLEEDITBASICSET(v);
   }*/
   
   return ret;
}

var INPTYPEOVERRIDE={
 
};

for(var mem in INPTYPEOVERRIDE)
{
   INPTYPES[mem]=INPTYPEOVERRIDE[mem];
}
function RMFgetCard(config,level){
   return CARDTYPES.GET(config.dataType || "", config.data || config.labelText || config.text || "", level || 0);
}

var CARDTYPES={
   GET:function(type, data, level){
      if(this[type]){
         return this[type](data, level);
      }return this.BASIC(data,level);
   },
   BASIC:function(data, level){
      //alert("here"+data)
      var txt;
      if(typeof data !== "object"){
         txt= _el.TEXT(data);
      }else{
         txt= _el.TEXT(''+JSON.stringify(data));
      }
      return _el.CREATE('span','','cardBasic',{},[txt]);
   },
   CURRY:function(type){
     var t=this;
     return function(data, level){
        return t.GET(type, data, level);
     }
   }
}

function RMFgetValue(config){
   if(config.data && config.dataType){
      return VALUETYPES.GET(config.dataType, config.data);
   }return config.value || "";
}

var VALUETYPES={
   GET:function(type, data){
      if(this[type]){
         return this[type](data);
      }return this.BASIC(data);
   },
   BASIC:function(data){
     if(typeof data === "object"){
        if(data.pk){
           return data.pk;
        }return JSON.stringify(data);
     }return ''+data;
   }
}

var RMF_makeTypeStack=[];
function MAKE(target, inps, formCol, nameStack, inpsConfig){
   nameStack=nameStack || "";
   inpsConfig= inpsConfig || {};
   var RMF_typeStackLength=RMF_makeTypeStack.length;
  // alert(nameStack+" "+JSON.stringify(inpsConfig));
   _el.EMPTY(target);
   for(var i=0; i<inps.length; i++)
   {
      RMF_makeTypeStack.splice(RMF_typeStackLength+1);
      //console.log(inps[i].type);
      var tp;
      if(['tableSearch', 'tableSearchForEdit'].indexOf(inps[i].type)> -1){
         tp=inps[i].typeName;
      }else{
         tp=inps[i].type;
      }
      if(tp.match(/^TBL_/) && RMF_makeTypeStack.indexOf(tp) > -1){
         continue;
      }
      RMF_makeTypeStack[RMF_typeStackLength]=tp;
      

      inps[i].nameStack=nameStack;
      inps[i].formCol=formCol;
     // alert(inps[i].name+": "+JSON.stringify(inpsConfig[inps[i].name]));
      var temp=INPTYPES[inps[i].type](_ob.COMBINE(inps[i], inpsConfig[inps[i].name] || {}));
      if(temp.COLL){
         formCol[inps[i].name]=temp;
      }
      _el.APPEND(target, temp.el);
   }
   RMF_makeTypeStack.splice(RMF_typeStackLength);
}

function RMF_fcCOLL(){
     var col = {};
         for(var mem in this)
         {
             if(this[mem]  && this[mem].COLL){
                col[mem]=this[mem].COLL();
             }
         }
         return col;
}
function RMF_fcSET(v){
   v= v || {};
   
   for(var mem in this)
   {
      if(v[mem] && this[mem]  && this[mem].SET){
         this[mem].SET(v[mem]);
      }
   }
   
}
function FORM_COL_OB(formCol){
   this.formCol=formCol;
   this.formCol.SET=RMF_fcSET;
   this.formCol.COLL=RMF_fcCOLL;
   this.formCol.IS_subFColl=true;
}
FORM_COL_OB.prototype.COLL=function(){
   
         var col={};
         for(var mem in this.formCol)
         {
             if(this.formCol[mem] &&  this.formCol[mem].COLL){
                col[mem]=this.formCol[mem].COLL();
             }
         }
         return col;
}
FORM_COL_OB.prototype.SET=function(v){
   v= v || {};
   
   for(var mem in this.formCol)
   {
	   /* this typeof undefined check could break things later that 
	   depend on this library. The check used to just be for if v[mem] was falsey
	   but empty strings and boolean false for checkboxes failed and integer 0. As well as the integer zero.
	   We will see what happens. 
	   
	   This check here is more what was desired. If nothing exists do not call SET. 
	   
	   If something breaks, we will need to make a splinter for the site generator. This check is not acceptable for the site generator, because of all of the checkboxes.
	   
	   */
      if(typeof v[mem] !== "undefined" && v[mem] !== null && this.formCol[mem]  && this.formCol[mem].SET){
         this.formCol[mem].SET(v[mem]);
      }
   }
}
FORM_COL_OB.prototype.SETforEdit=function(v){
   v=v || {};
   v.OGvalue=_ob.CLONE(v);
   this.SET(v);
}
FORM_COL_OB.prototype.SUBMIT=function(e){
   e=e || new Event("submit");
   this.form.onsubmit.call(this.form,e);
}
function RMFORM(target, inps, name, config){
   name=name || "defaultName";
   config=config || {};

   config.method=config.method || "POST";
   config.action=config.action || "";
   config.ajaxAction=config.ajaxAction || "";
   config.ajaxMethod=config.ajaxMethod || "POST";
   config.ajaxProc=config.ajaxProc || DUMMY_FUNCT;
   config.useModal=config.useModal || false;
   config.ajaxDacm=config.ajaxDacm || false;
   config.header=config.header || "";
   config.headerLevel=config.headerLevel || 1;
   config.submitFilter=config.submitFilter || function(a){return a;}

   var lockout=false;
   var formCol={};
   var ret=new FORM_COL_OB(formCol);

   _el.APPEND(target,[
      ret.form=_el.CREATE("form", "FORM-"+name,"",{
         action:config.action,
         method:config.method
      })
   ]);

   if(config.header){
      inps=_ob.CLONE(inps);
      inps.unshift({
        type:"header",
        text:config.header,
        level:config.headerLevel
      });
   }
   //alert("HHHHHH "+JSON.stringify(inps)+" .... "+JSON.stringify(config.inpsConfig));
   MAKE(ret.form, inps, formCol, name, config.inpsConfig);

   var osFunct="";
   
   if(config.ajaxAction || config.ajaxDacm){
     osFunct=function(e){
        e.preventDefault(); e.cancelBubble=true;
        var target;
        var dd;
        if(config.localProc){
           return localProc(ret.COLL());
           
        }

        if(config.ajaxDacm){
           dd=config.ajaxDacm;
        }else{
			
           dd=new dFCM(config.ajaxAction, {method:'POST', useFormData:true});
        }
        if(lockout){return;}
        if(config.useModal && dd){
            var modStr="Talking to "+dd.file+"... ";
            
            var modalHolder=RMF_MODAL(modStr);
            target=modalHolder.ticker;
        }else{
           target=this.parentNode;
        }
        dd.CAPTURE(config.viewController || VCR.main, target);
        lockout=true;
        dd.CALL_data(function(res){
      //      alert("sent");
         //  alert("from send: "+res);
           lockout=false;
           config.ajaxProc(res);
           if(config.useModal){
              modalHolder.CLOSE();
           }
        },{dat:config.submitFilter(ret.COLL())});
       // alert(JSON.stringify(ret.COLL()));
       
     }
   }

   if(config.collProc){
      ret.collProc=config.collProc;
      osFunct=function(e){
        e.preventDefault(); e.cancelBubble=true;
        config.collProc(ret.COLL());
      }
   }

   ret.form.onsubmit=osFunct;
   _el.APPEND(ret.form, _el.CREATE('button','','rmfSubmitButton',{},['Submit']));
   
   return ret;

}
var RMF_MODAL=function(text){
   
   var ret={
      CLOSE:function(){ _el.REMOVE(this.wrapper);},
      APPEND:function(v){_el.APPEND(this.appendage, v)}
   };

    ret.wrapper=_el.CREATE('div','','RMF_modalWrapper',{},[
       ret.backer=_el.CREATE('div','','RMF_modalBacker'),
       ret.client=_el.CREATE('div','','RMF_modalClient',{},[
          ret.ticker=_el.CREATE('div','','RMF_modalTicker'),
          ret.appendage=_el.CREATE('div','','RMF_modalAppendage')
       ])
    ]);
    ret.APPEND(_el.TEXT(text));
   _el.APPEND(el_body, ret.wrapper);
   return ret;
}
// functions that call the FORM function with certain configurations

function RMF_typeForm(typeName,target, name, config){
   return RMFORM(target, name, INPTYPES[typeName], config);
}

function RMF_actionProcedureForm(action, typeName,target, name, config){
   var dd=new dFCM("/RMF/"+typeName+"/action.php?action="+action, {method:'POST', headers:{'Content-Type':'special/obPost'}});
   
   config.ajaxDacm=dd;
 
   return RMFORM(target, INPTYPES_inpLists[typeName+action],name,config);
}


</script>
<script id='js_library/softNotification'>
SoftNotification={
   Render:function(body, fadeOutDur){
      fadeOutDur=fadeOutDur || 1000;
      var r;
      _el.APPEND(document.body, r=_el.CREATE('div','','SoftNotification-Wrapper',{},[
         _el.CREATE('div','','SoftNotification-ActionWrapper',{},[_el.CREATE('button','','',{onclick:function(){_el.REMOVE(this.parentNode.parentNode);}},["X"])]),
         _el.CREATE('div','','SoftNotification-BodyWrapper',{},body)
      ]));
      r.style.opacity='0';
      setTimeout(function(){
         r.style.opacity='1';
         if(fadeOutDur === -1){return;}
         setTimeout(function(){
            r.style.opacity="0";
            setTimeout(function(){_el.REMOVE(r);},501);
         },fadeOutDur+501);
      },1);
   }
};
</script>
<script id='js_library/clipboardLib'>
_ClipLib={
   Copy:function(txt, onSuccess, onFail){
     onFail=onFail || function(txt){console.error(txt);};
     onSuccess=onSuccess || function(txt){console.log(txt);};
     if (navigator.clipboard) {
       navigator.clipboard.writeText(txt)
         .then(() => {
           onSuccess('Text has been copied to the clipboard');
         })
         .catch(err => {
           onFail('Unable to copy text to the clipboard:');
         });
     } else {
        // Create a new textarea element to temporarily hold the text
        const textarea = document.createElement('textarea');
        textarea.value = txt;

        // Make the textarea invisible
        textarea.style.position = 'absolute';
        textarea.style.left = '-9999px';
        textarea.style.visibility="hidden";

        // Append the textarea to the document
        document.body.appendChild(textarea);

        // Select the text inside the textarea
        textarea.select();

        try {
          // Execute the copy command
          document.execCommand('copy');
          onSuccess('Text has been copied to the clipboard');
        } catch (err) {
          onFail('Unable to copy text to the clipboard:');
        } finally {
          // Remove the textarea from the DOM
          document.body.removeChild(textarea);
        }
     
     }

   },
   CopySoftNotification:function(txt){
      this.Copy(txt, function(txt){SoftNotification.Render(txt);}, function(txt){SoftNotification.Render(txt);});
   }
};
</script>
<script id='js_library/elFetch'>
function ElFetch( target,fetchMessage, file, config, responseType,responseHandlers,disablers){
    /*
      target: element to be the target,
      fetchMessage: a node to be appended to the target, 
     
         file:string, 
         config (the config of the actual call to fetch): {body:string, method:'POST', etc...}, 
         responseType: string json | text 
    */
    /* responseHandlers ={
         success: function(result, target){} to be fired on success
         fail: function(error, target){} to be fired on failure,
         overrideMsg: if you only want to display a single message on failure
       }
    */
    config=config || {};
    disablers=disablers || {};
    if(disablers.button){
       disablers.button.setAttribute('disabled','');
    }else if(disablers.form){
       var oldListener=disablers.form.onsubmit;
       disablers.form.onsubmit=function(e){
            e.stopImmediatePropagation();
            e.preventDefault();
            e.cancelBubble=true;
       }
    }else if(disablers.fieldset){
       disablers.fieldset.setAttribute('disabled','');
    }
    if(typeof fetchMessage === 'string'){fetchMessage=_el.TEXT(fetchMessage);}
    _el.APPEND(target, fetchMessage);
    fetch(file,config)
    .then(function(res){
        _el.REMOVE(fetchMessage);
        console.log("fetchResult:",res, res.status);
        if(parseInt(res.status) >= 400){
            console.log(file+" errorStatus: "+res.status);
            throw new Error("Server Error "+res.status);
        }
        return res[responseType]();
    }).then(function(rt){
        if(disablers.button){
           disablers.button.removeAttribute('disabled');
        }else if(disablers.form){
           disablers.form.onsubmit=oldListener;
        }else if(disablers.fieldset){
           disablers.fieldset.removeAttribute('disabled');
        }
        if((responseType === 'json' &&  rt.success) || rt === "SUCCESS"){
            _el.REMOVE(fetchMessage);
            responseHandlers.success(rt,target);
        }else{
            console.log('error', rt);
            var err= new Error(responseHandlers.overrideMsg || "Error Processing: "+((responseType === "json") ? (rt.msg || '') : rt || ''));
            err.dat=rt;
            throw err;
        }
        
    }).catch(function(e){
        _el.REMOVE(fetchMessage);
        if(disablers.button){
           disablers.button.removeAttribute('disabled');
        }else if(disablers.form){
           disablers.form.onsubmit=oldListener;
        }else if(disablers.fieldset){
           disablers.fieldset.removeAttribute('disabled');
        }
        console.log(e, e.dat || '');
        if(responseHandlers.fail){
           setTimeout(responseHandlers.fail(e,target), 1);
        }
        _el.REMOVE(fetchMessage);
        var m=e.message;
        if(e.message === 'Failed to fetch'){
            m=("There was a problem submitting. Possibly a network error. Please try again.");
        }
        console.log(file+" Fetch Error: "+m);
		if(!responseHandlers.quietError){
			_el.APPEND(target, m);
		}
    });
}
</script>
<style id='js_library/rmfCSS'>
.RMFhiderClosed{
   height:0;
   overflow:hidden;
}

.RMFhiderLabel{
   cursor:pointer;
}
.basicModalParent{
   position:fixed;
   left:0;
   top:0;
   width:100%;
   height:100%;
}
.basicModalBacker{
   position:absolute;
   left:0;
   top:0;
   width:100%;
   height:100%;
   background-color:black;
   opacity:0.5;
}
.basicModalClient{
   box-sizing:border-box;
   position:absolute;
   left:5%;
   width:90%;
   top:5%;
   height:90%;
   background-color:seashell;
   overflow:scroll;
   padding-top:8vh;
}
.basicModalCloser{
   font-size:150%;
   position:fixed;
   right:8%;
   top:2%;
   padding:3px;
   cursor:pointer;
   background-color:white;
}



.RMF_modalWrapper{
   position:fixed;
   left:0; top:0; width:100%; height:100%;
}

.RMF_modalBacker{
   position:absolute; 
   left:0; top:0; width:100%; height:100%;
   opacity:0;
}

.RMF_modalClient{
   position:absolute;
   left:3%;
   top:3%;
   height:94%; width:40%;
   color:white;
   text-shadow: 0 0 30px white;
}

.RMF_modalTicker{
   display:inline-block;
   
}

.RMF_modalAppendage{
   display:inline-block;
   background-color:tan;
   padding:8px;
}
.cardBasic{
   margin:4px;
   padding:1px;
}
.cardBasic:empty{
	margin:0; padding:0;
}

.tableSearchsectionLabel{
  border:3px ridge gold;
  padding:2px;
  margin-top:3px;
  margin-bottom:20px;
}

.tableSearchsectionLabel+.tableSearchsectionLabel{
   border-top:3px solid #00ff00;
   
}

.tableSearchsectionLabel .tableSearchsectionLabel{
   border:3px ridge green;
}
.tableSearchsectionLabel .tableSearchsectionLabel .tableSearchsectionLabel{
   border:3px ridge pink;
}

.tableSearchResult{
   border:1px solid black;
   margin:4px;
   padding:2px;
   display:inline-block;
}


.RMFdynamicOnCheckboxFallthroughsectionLabel{
  border:1px dotted black;
}


</style>
<style id='js_library/softNotificationCSS'>
.SoftNotification-Wrapper{
   background-color:lightBlue;
   border:2px solid orange;
   border-radius:10px;
   position:fixed;
   bottom:1%;
   right:1%;
   max-width:300px;
   padding:8px;
   transition:opacity 500ms;
   color:black;
   
}

.SoftNotification-ActionWrapper{
   text-align:right; 
   padding:2px;
   margin-bottom:3px;
}
.SoftNotification-ActionWrapper button{

}

.SoftNotification-BodyWrapper{
   padding:2px;
}
</style>
<style id='CSS'>
#hiddenForm{
   display:none;
}
#resultPre{
	padding:10px;
	overflow-x:scroll;
	border:1px solid brown;
}

form{
   margin:10px;
   border:2px solid blue;
   padding:4px;
}
form button{
   margin-top:10px;
   margin-bottom:10px;
}
form input{
   margin-top:10px;
   margin-bottom:10px;
}
details{
   margin:10px;
   margin-bottom:20px;
}
details button{
   margin:10px;
}

</style>
<script id=''>
VCR.main= new VC(

undefined,

{},

{})
VCR.main.REGISTER_view(

"home",

function(a){
   var rmfTar;
   
   // forms
   _el.APPEND(a.GET_target(),[
      _el.CREATE('div','','',{},[
         "INSTRUCTIONS: Fill out the form and submit! Specify an output file to put the output into a file on your server."
      ]),	   
      rmfTar=_el.CREATE('div'),
      comTar=_el.CREATE('div'),
	  
	  // hidden form, for actual submission
      hiddenForm=_el.CREATE('form','hiddenForm','',{method:"POST"},[
         dat=_el.CREATE('textarea','','',{name:'dat'})
      ])
   ]);

   
   var rmf=RMFORM(rmfTar, [
      {type:'singleLine', name:'fileName', labelText:'Output File Name: '},
      {type:'dynamicList', name:'list', labelText:'Concat Files:', addText:'+Add File'}
   ], "list", {
      collProc:function(c){
		// set hidden form and submit
         dat.value=JSON.stringify(c);
         hiddenForm.submit();
      }
   });
   
   // sets RMF if a submission exists, so errors can be corrected if needed
   rmf.SET(<?php echo json_encode($dat ?? []);?>);
   
},

{},

undefined)

</script>
</HEAD>
<body id='BODY' onload='IGNITE();' >
<div><?php 

	// result output
	if($err ?? false){
		echo "Errors: <br>".join("<br>",$err)."<br><br>";
	}else if($_POST['dat'] ?? false){
		echo "SUCCESS! <br><br>";
	}
	
	if($_POST['dat'] ?? false){
		// report results in document
		?>
		<details>
			<summary>Results</summary>
			<button onclick="_ClipLib.Copy(document.getElementById('resultPre').textContent, function(){SoftNotification.Render('Copy Success');});">Copy Results</button>
			<br>
			<pre id="resultPre">
<?php echo htmlentities($conc); ?>
			</pre>
		</details>
		<?php
	}
?></div>

</body>
</HTML>
